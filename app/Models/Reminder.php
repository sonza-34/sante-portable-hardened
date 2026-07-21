<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'title', 'description',
        'due_at', 'is_completed',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope pour les rappels à venir
    public function scopeUpcoming($query)
    {
        return $query->where('due_at', '>=', now())
                     ->where('is_completed', false)
                     ->orderBy('due_at');
    }
}