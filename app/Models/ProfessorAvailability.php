<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessorAvailability extends Model
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

    /**
     * PROF_AVAILABILITY_08H_23H_V2
     *
     * Créneaux fixes utilisés pour construire le planning.
     * 10 blocs continus de 1h30, de 08:00 à 23:00.
     */
    public const TIME_SLOTS = [
        ['start' => '08:00', 'end' => '09:30'],
        ['start' => '09:30', 'end' => '11:00'],
        ['start' => '11:00', 'end' => '12:30'],
        ['start' => '12:30', 'end' => '14:00'],
        ['start' => '14:00', 'end' => '15:30'],
        ['start' => '15:30', 'end' => '17:00'],
        ['start' => '17:00', 'end' => '18:30'],
        ['start' => '18:30', 'end' => '20:00'],
        ['start' => '20:00', 'end' => '21:30'],
        ['start' => '21:30', 'end' => '23:00'],
    ];

    protected $fillable = [
        'prof_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'prof_id' => 'integer',
        'day_of_week' => 'integer',
    ];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prof_id');
    }

    public function getDayLabelAttribute(): string
    {
        return self::DAYS[(int) $this->day_of_week] ?? 'Jour';
    }

    public function getStartLabelAttribute(): string
    {
        return Carbon::parse($this->start_time)->format('H:i');
    }

    public function getEndLabelAttribute(): string
    {
        return Carbon::parse($this->end_time)->format('H:i');
    }

    public function getRangeLabelAttribute(): string
    {
        return $this->start_label . ' – ' . $this->end_label;
    }

    public static function timeSlots(): array
    {
        return collect(self::TIME_SLOTS)
            ->values()
            ->map(function (array $slot, int $index) {
                return [
                    'index' => $index + 1,
                    'key' => $slot['start'] . '-' . $slot['end'],
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                    'label' => $slot['start'] . ' – ' . $slot['end'],
                ];
            })
            ->all();
    }
}
