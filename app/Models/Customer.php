<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'cpf',
        'phone',
        'email',
        'address',
        'notes',
        'payment_preference',
        'created_by',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
