<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'duracao_minutos' => 'nullable|integer|min:1'
        ];
    }
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser texto.',
            'numeric' => 'O campo :attribute deve ser numérico.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'min' => 'O campo :attribute deve ser no mínimo :min.'
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'preco' => 'preço',
            'duracao_minutos' => 'duração em minutos'
        ];
    }
}
