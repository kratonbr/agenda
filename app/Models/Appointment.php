<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    // Isso libera esses campos para serem salvos pelo formulário
    protected $fillable = [
        'user_id',
        'customer_name',
        'phone',
        'scheduled_at',
        'status',
        'notes',
    ];
    // Isso avisa ao Laravel: "Trate o scheduled_at como data, não como texto"
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
    // --------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}