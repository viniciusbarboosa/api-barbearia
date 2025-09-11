<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required','string','min:8','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/'],
            'tipo_usuario' => 'required|string|in:B,U',
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'A senha deve conter: Mínimo 8 caracteres,1 letra maiúscula,1 minúscula,1 número,1 caractere especial (@$!%*?&)',
            'tipo_usuario.in' => 'O tipo de usuário deve ser B (Barbeiro) ou U (Usuário)'
        ];
    }
}
