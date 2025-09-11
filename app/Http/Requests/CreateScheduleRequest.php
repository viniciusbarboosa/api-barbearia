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
}
