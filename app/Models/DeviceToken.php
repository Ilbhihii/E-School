<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'token_hash',
        'platform',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    protected static function booted(): void
    {
        static::saving(function (DeviceToken $deviceToken) {
            if (trim((string) $deviceToken->token) !== '') {
                $deviceToken->token_hash = static::hashToken(
                    (string) $deviceToken->token
                );
            }
        });
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', trim($token));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour les tokens actifs uniquement
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope par plateforme
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }
}
