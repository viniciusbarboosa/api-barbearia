<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'string|max:100',
            'preco' => 'numeric|min:0.01',
            'duracao_minutos' => 'nullable|integer|min:1',
            'ativo' => 'boolean'
        ];
    }
    public function messages(): array
    {
        return [
            'string' => 'O campo :attribute deve ser texto.',
            'numeric' => 'O campo :attribute deve ser numérico.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.'
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'preco' => 'preço',
            'duracao_minutos' => 'duração em minutos',
            'ativo' => 'ativo'
        ];
    }
}
