<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarberSchedule extends Model
{
    protected $fillable = [
    'user_id',
    'date',
    'start_time',
    'end_time',
    'available'
    ];
}
