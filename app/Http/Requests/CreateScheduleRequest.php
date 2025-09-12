<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required_without:data|date|after_or_equal:today',
            'data' => 'required_without:date|date|after_or_equal:today',
            'start_time' => 'required_without:horario_inicio_expediente|date_format:H:i',
            'horario_inicio_expediente' => 'required_without:start_time|date_format:H:i',
            'end_time' => 'required_without:horario_fim_expediente|date_format:H:i|after:horario_inicio_expediente',
            'horario_fim_expediente' => 'required_without:end_time|date_format:H:i|after:horario_inicio_expediente'
        ];
    }
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'date_format' => 'O campo :attribute deve ter o formato :format.',
            'after_or_equal' => 'O campo :attribute deve ser uma data igual ou posterior a hoje.',
            'after' => 'O campo :attribute deve ser uma hora posterior ao início.'
        ];
    }

    public function attributes(): array
    {
        return [
            'date' => 'date',
            'data' => 'date',
            'horario_inicio_expediente' => 'start time',
            'start_time' => 'start time',
            'horario_fim_expediente' => 'end time',
            'end_time' => 'end time'
        ];
    }
}
