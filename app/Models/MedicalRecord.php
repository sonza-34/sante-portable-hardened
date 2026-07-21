<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'record_code', 'height_cm', 'weight_kg',
        'chronic_conditions', 'allergies', 'current_treatments',
        'family_history', 'surgical_history',
        'emergency_contact_name', 'emergency_contact_phone',
        'insurance_provider', 'insurance_number',
    ];

    protected $casts = [
        'height_cm' => 'decimal:2',
        'weight_kg' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }
}