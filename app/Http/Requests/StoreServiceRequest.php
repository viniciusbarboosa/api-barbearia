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
}
