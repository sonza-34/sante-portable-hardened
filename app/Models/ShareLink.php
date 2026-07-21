<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ShareLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'doctor_id', 'token', 'duration',
        'expires_at', 'access_count', 'last_accessed_at',
        'last_accessed_ip', 'is_revoked', 'notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    /**
     * Hash une IP avec la clé app pour stockage non-réversible (RGPD).
     * Permet quand même de compter des accès uniques par IP et de détecter
     * des anomalies (beaucoup d'IPs distinctes) sans stocker l'IP en clair.
     */
    public static function hashIp(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }
        return hash('sha256', $ip . config('app.key'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Crée un lien de partage. Token = 64 chars (256 bits) — largement suffisant
     * pour un secret médical. Le record_code et le token sont indépendants.
     */
    public static function createFor(User $user, string $duration, ?int $doctorId, ?string $notes): self
    {
        $hours = match ($duration) {
            '1h' => 1,
            '7d' => 24 * 7,
            default => 24,
        };

        return self::create([
            'user_id'    => $user->id,
            'doctor_id'  => $doctorId,
            'token'      => Str::random(64),
            'duration'   => $duration,
            'expires_at' => now()->addHours($hours),
            'notes'      => $notes,
        ]);
    }

    public function isValid(): bool
    {
        return !$this->is_revoked && $this->expires_at->isFuture();
    }

    /**
     * Enregistre un accès. L'IP est hashée avant stockage.
     */
    public function recordAccess(?string $ip, ?string $userAgent = null): void
    {
        $this->update([
            'access_count' => $this->access_count + 1,
            'last_accessed_at' => now(),
            'last_accessed_ip' => self::hashIp($ip),
        ]);

        // Log d'audit pour traçabilité
        Log::channel('medical')->info('share.access', [
            'share_link_id' => $this->id,
            'patient_id' => $this->user_id,
            'doctor_id' => $this->doctor_id,
            'ip_hash' => self::hashIp($ip),
            'user_agent' => $userAgent,
        ]);
    }
}
