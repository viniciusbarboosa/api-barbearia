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
            'name' => 'sometimes|string|max:100',
            'nome' => 'sometimes|string|max:100',
            'price' => 'sometimes|numeric|min:0.01',
            'preco' => 'sometimes|numeric|min:0.01',
            'duration_minutes' => 'nullable|integer|min:1',
            'duracao_minutos' => 'nullable|integer|min:1',
            'active' => 'sometimes|boolean',
            'ativo' => 'sometimes|boolean'
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
            'name' => 'name',
            'nome' => 'nome',
            'price' => 'price',
            'preco' => 'preço',
            'duration_minutes' => 'duração em minutos',
            'duracao_minutos' => 'duração em minutos',
            'active' => 'active',
            'ativo' => 'ativo'
        ];
    }
}
