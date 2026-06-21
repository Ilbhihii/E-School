<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAppointment extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'status',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
