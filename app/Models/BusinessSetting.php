<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'requires_cpf',
        'enable_medical_fields',
        'show_payment_options',
        'default_appointment_duration',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
