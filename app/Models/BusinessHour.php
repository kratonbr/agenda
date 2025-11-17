<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    use HasFactory;

    // 1. Proteção: Liberamos estes campos para serem salvos em massa
    protected $fillable = [
        'user_id',
        'day',       // 0 a 6
        'open_at',   // Horário de abertura
        'close_at',  // Horário de fechamento
        'is_open',   // Se está aberto (true/false)
    ];

    // 2. Conversão Automática: Transforma texto do banco em objetos Carbon (Data/Hora)
    // Isso permite usar ->format('H:i') na View sem erros.
    protected $casts = [
        'open_at' => 'datetime', 
        'close_at' => 'datetime',
        'is_open' => 'boolean',
    ];

    // 3. Relação: Um horário pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}