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
                'aprovado' => false
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
}
