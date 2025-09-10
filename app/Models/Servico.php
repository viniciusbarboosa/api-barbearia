<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos massivamente (create/update)
     */
    protected $fillable = [
        'user_id',
        'nome',
        'preco',
        'descricao',
        'duracao_minutos',
        'ativo'         
    ];

    /**
     * Relacionamento opcional com User (sem FK no banco)
     * Se o user_id existir na tabela users, retorna o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Escopo para filtrar serviços ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Escopo para serviços de um barbeiro específico
     */
    public function scopeDoBarbeiro($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }
}
