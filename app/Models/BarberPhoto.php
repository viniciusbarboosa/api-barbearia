<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'foto_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
