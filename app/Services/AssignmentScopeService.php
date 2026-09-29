<?php

namespace App\Services;

use App\Models\Live;
use App\Models\ProfAssignment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AssignmentScopeService
{
    public function professorCode(ProfAssignment $a): ?string
    {
        $a->loadMissing(['subject', 'classRoom', 'classSlot']);

        return $this->build(
            (int) $a->day_of_week,
            (string) $a->start_time,
            (string) ($a->subject?->name ?? ''),
            (string) ($a->classRoom?->name ?? ''),
            (string) ($a->classSlot?->code ?? '')
        );
    }

    public function liveCode(Live $live): ?string
    {
        $live->loadMissing([
            'classSlot.subject',
            'classSlot.classRoom',
            'classRoom',
        ]);

        if (!$live->live_date) return null;

        try {
            $day = Carbon::parse($live->live_date)->dayOfWeekIso;
        } catch (\Throwable $e) {
            return null;
        }

        return $this->build(
            $day,
            (string) $live->start_time,
            (string) ($live->classSlot?->subject?->name ?? ''),
            (string) (
                $live->classSlot?->classRoom?->name
                ?? $live->classRoom?->name
                ?? ''
            ),
            (string) ($live->classSlot?->code ?? '')
        );
    }

    public function normalizeTime(?string $value): ?string
    {
        try {
            return Carbon::parse((string) $value)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function build(
        int $day,
        string $time,
        string $subject,
        string $className,
        string $group
    ): ?string {
        $days = [
            1 => 'L',
            2 => 'MA',
            3 => 'M',
            4 => 'J',
            5 => 'V',
            6 => 'S',
            7 => 'D',
        ];

        if (!isset($days[$day])) return null;

        $time = $this->normalizeTime($time);
        if (!$time) return null;

        [$h, $m] = array_map('intval', explode(':', $time));
        $minutes = $h * 60 + $m;
        $first = 8 * 60;

        if (
            $minutes < $first
            || $minutes > 22 * 60
            || (($minutes - $first) % 30) !== 0
        ) {
            return null;
        }

        $number = intdiv($minutes - $first, 30) + 1;

        $subjectCode = substr(
            preg_replace(
                '/[^A-Z0-9]/',
                '',
                strtoupper(Str::ascii(trim($subject)))
            ),
            0,
            2
        );

        if ($subjectCode === '') $subjectCode = 'MT';
        if (strlen($subjectCode) === 1) $subjectCode .= 'X';

        $classNormalized = strtolower(Str::ascii(trim($className)));

        if (str_contains($classNormalized, 'debut')) {
            $classCode = 'D';
        } elseif (str_contains($classNormalized, 'inter')) {
            $classCode = 'I';
        } elseif (str_contains($classNormalized, 'avance')) {
            $classCode = 'A';
        } else {
            $classCode = substr(
                preg_replace(
                    '/[^A-Z0-9]/',
                    '',
                    strtoupper(Str::ascii(trim($className)))
                ),
                0,
                1
            ) ?: 'X';
        }

        preg_match('/(\d+)$/', strtoupper(trim($group)), $match);
        $groupNumber = $match[1] ?? null;
        if (!$groupNumber) return null;

        return $days[$day]
            . $number
            . $subjectCode
            . $classCode
            . $groupNumber;
    }
}