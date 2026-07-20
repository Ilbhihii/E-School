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
        'city',
        'country',
        'type',
        'status',
        'vocal_test_submission_id',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_TEST = 'test';
    const TYPE_INFORMATION = 'information';
    const TYPE_COMMUNICATION = 'communication';
    const TYPE_OTHER = 'other';

    public static function getTypes(): array
    {
        return [
            self::TYPE_TEST          => 'Test de niveau / Entretien',
            self::TYPE_INFORMATION   => 'Prendre des informations',
            self::TYPE_COMMUNICATION => 'Communication avec l\'administration',
            self::TYPE_OTHER         => 'Autre',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function vocalSubmission()
    {
        return $this->belongsTo(VocalTestSubmission::class, 'vocal_test_submission_id');
    }
}
