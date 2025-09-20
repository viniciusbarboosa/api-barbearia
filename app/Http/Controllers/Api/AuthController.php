<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetCode;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        Log::info('Dados recebidos:', $request->all());

        try {
            $data = $request->validated();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'user_type' => $data['user_type'],
                'password' => Hash::make($data['password']),
                'approved' => false
            ]);

            return response()->json([
                'success' => true,
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Erro ao processar a requisição'], 500);
        }
    }

    /**
     * Send a numeric code to the user's email to recover password.
     */
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'E-mail não encontrado.'], 404);
        }

        // generate 6-digit numeric code
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // store in password_reset_tokens table (upsert)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($code), 'created_at' => Carbon::now()]
        );

        // send email (using configured mail driver). Use mailable with branding
        try {
            Mail::to($user->email)->send(new PasswordResetCode($code, $user->name));
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de recuperação: ' . $e->getMessage());
            // if mail fails, still return success message to avoid leaking existence
        }

        return response()->json(['message' => 'Se o e-mail existir, você receberá instruções em breve.'], 200);
    }

    /**
     * Reset password using email, code and new password
     */
    public function resetWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$record) {
            return response()->json(['message' => 'Código inválido ou expirado.'], 400);
        }

        // check expiration (15 minutes)
        $created = Carbon::parse($record->created_at);
        if ($created->diffInMinutes(Carbon::now()) > 15) {
            return response()->json(['message' => 'Código expirado. Solicite um novo código.'], 400);
        }

        if (!Hash::check($request->code, $record->token)) {
            return response()->json(['message' => 'Código inválido.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Senha atualizada com sucesso.'], 200);
    }

    public function login(LoginRequest $request)
    {
        Log::info('Dados recebidos no login', $request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciais Inválidas'], 400);
        }

        $user = $request->user();
        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json(['message' => 'Login realizado com sucesso!', 'token' => $token, 'user' => $user], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso!']);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'old_password' => 'nullable|string',
            'password' => [
                'nullable',
                'string',
                'confirmed',
                Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->symbols()
            ],
        ]);

        $user->name = $request->name;

        if ($request->filled('password')) {

            if (!$request->filled('old_password')) {
                return response()->json(['message' => 'Para alterar a senha, você precisa informar sua senha atual.'], 422);
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['message' => 'A senha atual está incorreta.'], 422);
            }

            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user
        ]);
    }
}
