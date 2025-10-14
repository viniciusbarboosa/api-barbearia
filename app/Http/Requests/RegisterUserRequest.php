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
            'user_type' => 'required|string|in:B,U',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser texto.',
            'email' => 'O campo :attribute deve ser um e-mail válido.',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'password.regex' => 'A senha deve conter: mínimo 8 caracteres, 1 letra maiúscula, 1 minúscula, 1 número e 1 caractere especial (@$!%*?&).',
            'user_type.in' => 'O tipo de usuário deve ser B (Barbeiro) ou U (Usuário)'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'e-mail',
            'password' => 'password',
            'user_type' => 'user type'
        ];
    }
}
