<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Live extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'class_id',
        'stream_url',
        'provider',
        'admin_id',
        'user_id',
        'live_date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'live_date' => 'date',
    ];

    public function classRoom()
    {
        return $this->belongsTo(
            ClassRoom::class,
            'class_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Date et heure exactes de début dans le fuseau de l'application.
     */
    public function getStartDateTimeAttribute()
    {
        if (!$this->live_date) {
            return null;
        }

        $date = Carbon::parse($this->live_date)
            ->format('Y-m-d');

        $time = $this->normalizeTime(
            $this->start_time,
            '00:00:00'
        );

        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $date . ' ' . $time,
            config('app.timezone')
        );
    }

    /**
     * Date et heure exactes de fin.
     *
     * En l'absence d'une heure de fin, la session dure une heure.
     * Si l'heure de fin est inférieure à l'heure de début, la fin est
     * considérée comme étant le lendemain.
     */
    public function getEndDateTimeAttribute()
    {
        $start = $this->start_date_time;

        if (!$start) {
            return null;
        }

        if (!$this->end_time) {
            return $start->copy()->addHour();
        }

        $end = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $start->format('Y-m-d')
                . ' '
                . $this->normalizeTime(
                    $this->end_time,
                    $start->copy()
                        ->addHour()
                        ->format('H:i:s')
                ),
            config('app.timezone')
        );

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return $end;
    }

    /**
     * Valeurs possibles :
     * - unscheduled : aucune date
     * - upcoming    : pas encore commencée
     * - live        : entre l'heure de début et l'heure de fin
     * - ended       : heure de fin dépassée
     */
    public function getScheduleStatusAttribute()
    {
        $start = $this->start_date_time;
        $end = $this->end_date_time;

        if (!$start || !$end) {
            return 'unscheduled';
        }

        $now = now();

        if ($now->lt($start)) {
            return 'upcoming';
        }

        if ($now->gte($end)) {
            return 'ended';
        }

        return 'live';
    }

    public function getIsLiveAttribute()
    {
        return $this->schedule_status === 'live';
    }

    public function getIsUpcomingAttribute()
    {
        return $this->schedule_status === 'upcoming';
    }

    public function getIsEndedAttribute()
    {
        return $this->schedule_status === 'ended';
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->schedule_status) {
            case 'live':
                return '🔴 EN DIRECT';

            case 'upcoming':
                return '⏳ À VENIR';

            case 'ended':
                return '✅ SESSION TERMINÉE';

            default:
                return '📅 À PROGRAMMER';
        }
    }

    /**
     * Les liens partagés par Teams personnel passent parfois par un lanceur
     * temporaire. Retourner directement l'URL de réunion évite les erreurs
     * d'ouverture lorsque ce lanceur n'est pas accessible.
     */
    public function getStreamUrlAttribute($value)
    {
        if (
            !$value
            || strtolower(
                (string) parse_url($value, PHP_URL_HOST)
            ) !== 'teams.live.com'
        ) {
            return $value;
        }

        if (
            parse_url($value, PHP_URL_PATH)
            !== '/dl/launcher/launcher.html'
        ) {
            return $value;
        }

        parse_str(
            (string) parse_url($value, PHP_URL_QUERY),
            $query
        );

        $meetingPath = $query['url'] ?? null;

        if (
            !$meetingPath
            || !str_starts_with(
                $meetingPath,
                '/_#/meet/'
            )
        ) {
            return $value;
        }

        return 'https://teams.live.com/'
            . ltrim(
                str_replace('/_#/', '/', $meetingPath),
                '/'
            );
    }

    public function getTeamsAppUrlAttribute()
    {
        $url = $this->stream_url;

        $host = strtolower(
            (string) parse_url($url, PHP_URL_HOST)
        );

        if (
            !in_array(
                $host,
                [
                    'teams.microsoft.com',
                    'teams.live.com',
                ],
                true
            )
        ) {
            return null;
        }

        return preg_replace(
            '#^https://#i',
            'msteams://',
            $url
        );
    }

    private function normalizeTime(
        $time,
        $fallback
    ) {
        if (!$time) {
            return $fallback;
        }

        try {
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Throwable $exception) {
            return $fallback;
        }
    }
}