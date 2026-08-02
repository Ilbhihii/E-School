<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfAssignment extends Model
{
    public const DAYS = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    ];

    protected $fillable = [
        'prof_id',
        'level_id',
        'class_id',
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function prof(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'prof_id'
        );
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(
            Level::class,
            'level_id'
        );
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(
            ClassRoom::class,
            'class_id'
        );
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(
            Subject::class,
            'subject_id'
        );
    }

    public function getDayLabelAttribute(): string
    {
        return self::DAYS[
            (int) $this->day_of_week
        ] ?? 'Horaire non défini';
    }

    public function getTimeRangeLabelAttribute(): string
    {
        if (
            !$this->start_time
            || !$this->end_time
        ) {
            return 'Horaire non défini';
        }

        return Carbon::parse(
            $this->start_time
        )->format('H:i')
            . ' – '
            . Carbon::parse(
                $this->end_time
            )->format('H:i');
    }

    public function getHasScheduleAttribute(): bool
    {
        return
            !empty($this->day_of_week)
            && !empty($this->start_time)
            && !empty($this->end_time);
    }
}
