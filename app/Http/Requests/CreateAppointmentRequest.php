<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barbeiro_id' => 'required|integer',
            'servico_id' => 'required|integer',
            'horario_id' => 'required|integer',
            'data' => 'required|date'
        ];
    }
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'date' => 'O campo :attribute deve ser uma data válida.'
        ];
    }

    public function attributes(): array
    {
        return [
            'barbeiro_id' => 'barbeiro',
            'servico_id' => 'serviço',
            'horario_id' => 'horário',
            'data' => 'data'
        ];
    }
}
