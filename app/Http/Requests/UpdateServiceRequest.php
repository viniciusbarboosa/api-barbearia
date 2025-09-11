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
}
