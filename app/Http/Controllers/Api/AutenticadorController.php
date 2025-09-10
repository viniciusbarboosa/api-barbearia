<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AutenticadorController extends Controller
{
    public function criar(Request $request)
    {
        Log::info('Dados recebidos:', $request->all());

        try {
            // Validação com ordem ajustada
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => [
                    'required',
                    'string',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                    'min:8'
                ],
                'tipo_usuario' => 'required|string|in:B,U',
            ], [
                'password.required' => 'A senha é obrigatória',
                'password.string' => 'A senha deve ser um texto',
                'password.regex' => 'A senha deve conter: Mínimo 8 caracteres,1 letra maiúscula,1 minúscula,1 número,1 caractere especial (@$!%*?&)',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres',
                'tipo_usuario.in' => 'O tipo de usuário deve ser B (Barbeiro) ou U (Usuário)'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Criação do usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'tipo_usuario' => $request->tipo_usuario,
                'password' => Hash::make($request->password),
                'aprovado' => false
            ]);

            return response()->json([
                'success' => true,
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar a requisição'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);


        Log::info('Dados recebidos no login', $request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciais Inválidas'], 400);
        }

        $user = $request->user();
        $token = $user->createToken('token-name')->plainTextToken;

        //Log::info('Dados do usuário:', $user->toArray());

        return response()->json(['message' => 'Login realizado com sucesso!', 'token' => $token, 'user' => $user], 200);
    }

    public function deslogar(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso!']);
    }
}
