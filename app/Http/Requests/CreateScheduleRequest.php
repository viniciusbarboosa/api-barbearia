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
            'data' => 'required|date|after_or_equal:today',
            'horario_inicio_expediente' => 'required|date_format:H:i',
            'horario_fim_expediente' => 'required|date_format:H:i|after:horario_inicio_expediente'
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
            'data' => 'data',
            'horario_inicio_expediente' => 'horário início do expediente',
            'horario_fim_expediente' => 'horário fim do expediente'
        ];
    }
}
