<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentAccessSession extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'device_label',
        'content_type',
        'content_id',
        'content_title',
        'last_seen_at',
        'expires_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function isExpired(): bool
    {
        return
            !$this->expires_at
            || $this->expires_at->lte(now());
    }
}
