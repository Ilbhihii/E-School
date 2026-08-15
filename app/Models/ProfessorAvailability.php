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
     * Créneaux fixes de 1h30 demandés pour construire le planning.
     * Ils commencent à 09:00 et se terminent à 19:30.
     */
    public const TIME_SLOTS = [
        ['start' => '09:00', 'end' => '10:30'],
        ['start' => '10:30', 'end' => '12:00'],
        ['start' => '12:00', 'end' => '13:30'],
        ['start' => '13:30', 'end' => '15:00'],
        ['start' => '15:00', 'end' => '16:30'],
        ['start' => '16:30', 'end' => '18:00'],
        ['start' => '18:00', 'end' => '19:30'],
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
