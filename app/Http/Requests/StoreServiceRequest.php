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
                // Accept either English or Portuguese keys for backward compatibility
                'name' => 'required_without:nome|string|max:100',
                'nome' => 'required_without:name|string|max:100',
                'price' => 'required_without:preco|numeric|min:0.01',
                'preco' => 'required_without:price|numeric|min:0.01',
                'duration_minutes' => 'nullable|integer|min:1',
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
                'name' => 'name',
                'nome' => 'nome',
                'price' => 'price',
                'preco' => 'preço',
                'duration_minutes' => 'duration in minutes',
                'duracao_minutos' => 'duração em minutos'
        ];
    }
}
