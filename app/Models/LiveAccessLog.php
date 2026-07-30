<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveAccessLog extends Model
{
    protected $fillable = [
        'live_id',
        'user_id',
        'status',
        'reason',
        'ip_address',
        'user_agent',
    ];

    public function live(): BelongsTo
    {
        return $this->belongsTo(Live::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
