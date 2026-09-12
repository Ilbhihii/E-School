<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentBehaviorNote extends Model
{
    use HasFactory;

    public const TYPE_POSITIVE = 'positive';
    public const TYPE_NEGATIVE = 'negative';

    protected $fillable = [
        'professor_id',
        'student_id',
        'subject_id',
        'level_id',
        'class_room_id',
        'type',
        'points',
        'note',
        'noted_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'noted_at' => 'date',
    ];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'professor_id'
        );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(
            ClassRoom::class,
            'class_room_id'
        );
    }

    public function getSignedPointsAttribute(): int
    {
        return $this->type === self::TYPE_NEGATIVE
            ? -abs((int) $this->points)
            : abs((int) $this->points);
    }

    public function getIsPositiveAttribute(): bool
    {
        return $this->type === self::TYPE_POSITIVE;
    }
}
