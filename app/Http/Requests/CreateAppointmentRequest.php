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
}
