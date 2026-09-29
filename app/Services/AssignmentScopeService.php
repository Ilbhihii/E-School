<?php

namespace App\Services;

use App\Models\Live;
use App\Models\ProfAssignment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AssignmentScopeService
{
    public function professorCode(
        ProfAssignment $assignment
    ): ?string {
        $assignment->loadMissing([
            'subject',
            'classRoom',
            'classSlot',
        ]);

        return $this->build(
            (int)
                $assignment
                    ->day_of_week,
            (string)
                $assignment
                    ->start_time,
            (string) (
                $assignment
                    ->subject
                    ?->name
                ?? ''
            ),
            (string) (
                $assignment
                    ->classRoom
                    ?->name
                ?? ''
            ),
            (string) (
                $assignment
                    ->classSlot
                    ?->code
                ?? ''
            )
        );
    }

    public function liveCode(
        Live $live
    ): ?string {
        $live->loadMissing([
            'classSlot.subject',
            'classSlot.classRoom',
            'classRoom',
        ]);

        $day = null;

        if (
            !empty(
                $live
                    ->assignment_day_of_week
            )
        ) {
            $day =
                (int)
                    $live
                        ->assignment_day_of_week;
        } elseif ($live->live_date) {
            try {
                $day =
                    Carbon::parse(
                        $live->live_date
                    )->dayOfWeekIso;
            } catch (\Throwable $exception) {
                return null;
            }
        }

        $time =
            $live
                ->assignment_start_time
            ?: $live
                ->start_time;

        if (
            !$day
            || !$time
        ) {
            return null;
        }

        return $this->build(
            $day,
            (string) $time,
            (string) (
                $live
                    ->classSlot
                    ?->subject
                    ?->name
                ?? ''
            ),
            (string) (
                $live
                    ->classSlot
                    ?->classRoom
                    ?->name
                ?? $live
                    ->classRoom
                    ?->name
                ?? ''
            ),
            (string) (
                $live
                    ->classSlot
                    ?->code
                ?? ''
            )
        );
    }

    public function normalizeTime(
        ?string $value
    ): ?string {
        return app(
            PedagogicalTimeSlotService::class
        )->normalizeTime(
            $value
        );
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

        if (!isset($days[$day])) {
            return null;
        }

        $number =
            app(
                PedagogicalTimeSlotService::class
            )->slotNumber(
                $day,
                $time,
                true
            );

        if (!$number) {
            return null;
        }

        $subjectCode =
            substr(
                preg_replace(
                    '/[^A-Z0-9]/',
                    '',
                    strtoupper(
                        Str::ascii(
                            trim(
                                $subject
                            )
                        )
                    )
                ),
                0,
                2
            );

        if ($subjectCode === '') {
            $subjectCode = 'MT';
        }

        if (
            strlen(
                $subjectCode
            ) === 1
        ) {
            $subjectCode .= 'X';
        }

        $classNormalized =
            strtolower(
                Str::ascii(
                    trim(
                        $className
                    )
                )
            );

        if (
            str_contains(
                $classNormalized,
                'debut'
            )
        ) {
            $classCode = 'D';
        } elseif (
            str_contains(
                $classNormalized,
                'inter'
            )
        ) {
            $classCode = 'I';
        } elseif (
            str_contains(
                $classNormalized,
                'avance'
            )
            || str_contains(
                $classNormalized,
                'adulte'
            )
        ) {
            $classCode = 'A';
        } else {
            $classCode =
                substr(
                    preg_replace(
                        '/[^A-Z0-9]/',
                        '',
                        strtoupper(
                            Str::ascii(
                                trim(
                                    $className
                                )
                            )
                        )
                    ),
                    0,
                    1
                )
                ?: 'X';
        }

        preg_match(
            '/(\d+)$/',
            strtoupper(
                trim(
                    $group
                )
            ),
            $match
        );

        $groupNumber =
            $match[1]
            ?? null;

        if (!$groupNumber) {
            return null;
        }

        return
            $days[$day]
            . $number
            . $subjectCode
            . $classCode
            . $groupNumber;
    }
}