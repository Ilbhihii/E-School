<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSetting extends Model
{
    protected $fillable = [
        'announcement_enabled',
        'blocking_enabled',
        'message',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'announcement_enabled' => 'boolean',
        'blocking_enabled' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function isAnnouncementVisible(): bool
    {
        if (!$this->announcement_enabled || !$this->end_at) {
            return false;
        }

        return now()->lt($this->end_at);
    }

    public function isBlockingNow(): bool
    {
        if (
            !$this->blocking_enabled
            || !$this->start_at
            || !$this->end_at
        ) {
            return false;
        }

        return now()->gte($this->start_at)
            && now()->lt($this->end_at);
    }

    public function getDurationMinutesAttribute(): int
    {
        if (!$this->start_at || !$this->end_at) {
            return 0;
        }

        return $this->start_at->diffInMinutes($this->end_at);
    }
}
