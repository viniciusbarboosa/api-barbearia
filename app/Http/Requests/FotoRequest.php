<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto' => 'required|string',
            'descricao' => 'nullable|string',
            'description' => 'nullable|string'
        ];
    }
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser texto.'
        ];
    }

    public function attributes(): array
    {
        return [
            'foto' => 'photo',
            'descricao' => 'description',
            'description' => 'description'
        ];
    }
}
