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
            'barber_id' => 'required_without:barbeiro_id|integer',
            'barbeiro_id' => 'required_without:barber_id|integer',
            'service_id' => 'required_without:servico_id|integer',
            'servico_id' => 'required_without:service_id|integer',
            'schedule_id' => 'required_without:horario_id|integer',
            'horario_id' => 'required_without:schedule_id|integer',
            'date' => 'required_without:data|date',
            'data' => 'required_without:date|date'
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
            'barber_id' => 'barber',
            'barbeiro_id' => 'barbeiro',
            'service_id' => 'service',
            'servico_id' => 'servico',
            'schedule_id' => 'schedule',
            'horario_id' => 'horario',
            'date' => 'date',
            'data' => 'data'
        ];
    }
}
