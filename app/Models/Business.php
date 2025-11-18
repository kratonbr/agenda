<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'type',
        'cnpj',
        'phone',
        'address',
        'plan',
        'active',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function businessHours()
    {
        return $this->hasMany(BusinessHour::class);
    }

    public function settings()
    {
        return $this->hasOne(BusinessSetting::class);
    }
}
