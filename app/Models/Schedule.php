<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    public const RECURRENCE_ONCE = 'once';
    public const RECURRENCE_WEEKLY = 'weekly';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'prof_id',
        'class_id',
        'slot_code',
        'subject_id',
        'level_id',
        'room_id',
        'subject',
        'start_time',
        'end_time',
        'date',
        'day_of_week',
        'recurrence',
        'valid_from',
        'valid_until',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date' => 'date',
        'day_of_week' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function prof(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prof_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subjectModel(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getDayLabelAttribute(): string
    {
        return [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ][$this->day_of_week] ?? '-';
    }


    public function getDurationMinutesAttribute(): int
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        return max(
            0,
            (int) $this->start_time->diffInMinutes($this->end_time)
        );
    }

    public function getDurationLabelAttribute(): string
    {
        $minutes = $this->duration_minutes;

        if ($minutes <= 0) {
            return '-';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours === 0) {
            return $minutes . ' min';
        }

        $hourLabel = $hours === 1 ? '1 heure' : $hours . ' heures';

        return $remainingMinutes > 0
            ? $hourLabel . ' ' . $remainingMinutes . ' min'
            : $hourLabel;
    }

    public function getTimeRangeLabelAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return '-';
        }

        return $this->start_time->format('H:i')
            . ' – '
            . $this->end_time->format('H:i');
    }

    public function getPathLabelAttribute(): string
    {
        $subject = $this->subjectModel?->name ?: $this->subject;
        $level = $this->level?->name ?: $this->classRoom?->level?->name;
        $class = $this->classRoom?->name;

        return collect([$subject, $level, $class])
            ->filter()
            ->implode(' → ');
    }

    /**
     * Le créneau est la partie horaire du parcours pédagogique.
     * Exemple : Dimanche · 09:00 – 10:30.
     */
    public function getSlotLabelAttribute(): string
    {
        $code = trim((string) $this->slot_code);
        $day = $this->day_label;
        $time = $this->time_range_label;

        $parts = collect([
            $code !== '' ? $code : null,
            $day !== '-' ? $day : null,
            $time !== '-' ? $time : null,
        ])->filter();

        return $parts->isEmpty()
            ? 'Créneau non défini'
            : $parts->implode(' · ');
    }

    /**
     * Parcours complet utilisé dans l'admin, le professeur,
     * l'étudiant et le front public.
     */
    public function getFullPathLabelAttribute(): string
    {
        return collect([
            $this->path_label,
            $this->slot_label,
        ])
            ->filter()
            ->implode(' → ');
    }
}
