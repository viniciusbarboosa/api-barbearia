<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioBarbearia extends Model
{
    protected $fillable = [
        'user_id',
        'data',
        'horario_inicio',
        'horario_fim',
        'disponivel'
    ];
}
