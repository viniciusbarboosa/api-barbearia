<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
    'user_id',
    'barber_id',
    'service_id',
    'schedule_id',
    'date',
    'status'
    ];

}
