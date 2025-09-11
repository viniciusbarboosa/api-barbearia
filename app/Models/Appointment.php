<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'barbeiro_id',
        'servico_id',
        'horario_id',
        'data',
        'status'
    ];

}
