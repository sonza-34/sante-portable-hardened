<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'vaccine_name', 'administration_date',
        'next_dose_date', 'batch_number', 'administered_by',
        'facility_name', 'notes',
    ];

    protected $casts = [
        'administration_date' => 'date',
        'next_dose_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}