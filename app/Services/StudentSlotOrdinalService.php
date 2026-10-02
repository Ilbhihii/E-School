<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StudentSlotOrdinalService
{
    private const TABLE =
        'pedagogical_time_slots';

    public function bootstrap(): void
    {
        if (
            !Schema::hasTable(
                self::TABLE
            )
        ) {
            return;
        }

        /*
         * Base historique :
         * 08:00, 08:30, 09:00...
         *
         * Un horaire libre (ex. 08:45) vient ensuite
         * s'insérer chronologiquement.
         */
        for (
            $day = 1;
            $day <= 7;
            $day++
        ) {
            for (
                $index = 0;
                $index <= 28;
                $index++
            ) {
                $time =
                    Carbon::createFromFormat(
                        'H:i',
                        '08:00'
                    )
                        ->addMinutes(
                            $index * 30
                        )
                        ->format('H:i:s');

                DB::table(
                    self::TABLE
                )->updateOrInsert(
                    [
                        'day_of_week' =>
                            $day,
                        'start_time' =>
                            $time,
                    ],
                    [
                        'slot_number' =>
                            $index + 1,
                        'updated_at' =>
                            now(),
                        'created_at' =>
                            now(),
                    ]
                );
            }
        }

        /*
         * Ramener également les horaires personnalisés
         * déjà enregistrés.
         */
        $this->importExistingTimes();

        for (
            $day = 1;
            $day <= 7;
            $day++
        ) {
            $this->reindexDay(
                $day,
                false
            );
        }

        for (
            $day = 1;
            $day <= 7;
            $day++
        ) {
            $this->synchronizeCodesForDay(
                $day
            );
        }
    }

    public function ensureSlot(
        int $day,
        string $time
    ): ?int {
        if (
            $day < 1
            || $day > 7
        ) {
            return null;
        }

        $normalized =
            $this->normalizeTime(
                $time
            );

        if (
            !$normalized
            || $normalized < '08:00'
            || $normalized > '22:00'
        ) {
            return null;
        }

        if (
            !Schema::hasTable(
                self::TABLE
            )
        ) {
            return
                $this->prospectiveNumber(
                    $day,
                    $normalized
                );
        }

        return DB::transaction(
            function () use (
                $day,
                $normalized
            ) {
                $exists =
                    DB::table(
                        self::TABLE
                    )
                        ->where(
                            'day_of_week',
                            $day
                        )
                        ->whereTime(
                            'start_time',
                            '=',
                            $normalized
                        )
                        ->exists();

                if (!$exists) {
                    DB::table(
                        self::TABLE
                    )->insert([
                        'day_of_week' =>
                            $day,
                        'start_time' =>
                            $normalized
                            . ':00',
                        'slot_number' =>
                            9999,
                        'created_at' =>
                            now(),
                        'updated_at' =>
                            now(),
                    ]);

                    /*
                     * L'ajout d'un horaire entre deux horaires
                     * existants décale automatiquement les numéros
                     * suivants.
                     */
                    $this->reindexDay(
                        $day,
                        true
                    );
                }

                return $this->numberFor(
                    $day,
                    $normalized,
                    false
                );
            }
        );
    }

    public function numberFor(
        int $day,
        string $time,
        bool $register = true
    ): ?int {
        $normalized =
            $this->normalizeTime(
                $time
            );

        if (
            !$normalized
            || $day < 1
            || $day > 7
        ) {
            return null;
        }

        if (
            Schema::hasTable(
                self::TABLE
            )
        ) {
            $number =
                DB::table(
                    self::TABLE
                )
                    ->where(
                        'day_of_week',
                        $day
                    )
                    ->whereTime(
                        'start_time',
                        '=',
                        $normalized
                    )
                    ->value(
                        'slot_number'
                    );

            if ($number) {
                return (int) $number;
            }
        }

        if ($register) {
            return $this->ensureSlot(
                $day,
                $normalized
            );
        }

        return $this->prospectiveNumber(
            $day,
            $normalized
        );
    }

    public function map(): array
    {
        if (
            Schema::hasTable(
                self::TABLE
            )
        ) {
            return DB::table(
                self::TABLE
            )
                ->orderBy(
                    'day_of_week'
                )
                ->orderBy(
                    'start_time'
                )
                ->get()
                ->groupBy(
                    'day_of_week'
                )
                ->map(
                    fn ($rows) =>
                        $rows
                            ->mapWithKeys(
                                fn ($row) => [
                                    substr(
                                        (string)
                                            $row
                                                ->start_time,
                                        0,
                                        5
                                    ) =>
                                        (int)
                                            $row
                                                ->slot_number,
                                ]
                            )
                            ->all()
                )
                ->all();
        }

        $result = [];

        for (
            $day = 1;
            $day <= 7;
            $day++
        ) {
            for (
                $index = 0;
                $index <= 28;
                $index++
            ) {
                $time =
                    Carbon::createFromFormat(
                        'H:i',
                        '08:00'
                    )
                        ->addMinutes(
                            $index * 30
                        )
                        ->format(
                            'H:i'
                        );

                $result[
                    $day
                ][
                    $time
                ] =
                    $index + 1;
            }
        }

        return $result;
    }

    public function normalizeTime(
        ?string $time
    ): ?string {
        $time =
            trim(
                (string) $time
            );

        if ($time === '') {
            return null;
        }

        try {
            return Carbon::parse(
                $time
            )->format(
                'H:i'
            );
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function prospectiveNumber(
        int $day,
        string $time
    ): int {
        $times =
            array_keys(
                $this->map()[
                    $day
                ]
                ?? []
            );

        $times[] =
            $time;

        $times =
            array_values(
                array_unique(
                    $times
                )
            );

        sort(
            $times,
            SORT_STRING
        );

        return
            array_search(
                $time,
                $times,
                true
            ) + 1;
    }

    private function importExistingTimes(): void
    {
        $sources = [];

        if (
            Schema::hasTable(
                'class_user'
            )
            && Schema::hasColumn(
                'class_user',
                'student_day_of_week'
            )
            && Schema::hasColumn(
                'class_user',
                'student_start_time'
            )
        ) {
            $sources[] =
                DB::table(
                    'class_user'
                )
                    ->whereNotNull(
                        'student_day_of_week'
                    )
                    ->whereNotNull(
                        'student_start_time'
                    )
                    ->get([
                        'student_day_of_week as day',
                        'student_start_time as time',
                    ]);
        }

        if (
            Schema::hasTable(
                'prof_assignments'
            )
        ) {
            $sources[] =
                DB::table(
                    'prof_assignments'
                )
                    ->whereNotNull(
                        'day_of_week'
                    )
                    ->whereNotNull(
                        'start_time'
                    )
                    ->get([
                        'day_of_week as day',
                        'start_time as time',
                    ]);
        }

        foreach (
            [
                'courses',
                'assignments',
                'lives',
            ]
            as $table
        ) {
            if (
                Schema::hasTable(
                    $table
                )
                && Schema::hasColumn(
                    $table,
                    'assignment_day_of_week'
                )
                && Schema::hasColumn(
                    $table,
                    'assignment_start_time'
                )
            ) {
                $sources[] =
                    DB::table(
                        $table
                    )
                        ->whereNotNull(
                            'assignment_day_of_week'
                        )
                        ->whereNotNull(
                            'assignment_start_time'
                        )
                        ->get([
                            'assignment_day_of_week as day',
                            'assignment_start_time as time',
                        ]);
            }
        }

        foreach ($sources as $rows) {
            foreach ($rows as $row) {
                $day =
                    (int) $row->day;

                $time =
                    $this->normalizeTime(
                        $row->time
                    );

                if (
                    $day < 1
                    || $day > 7
                    || !$time
                    || $time < '08:00'
                    || $time > '22:00'
                ) {
                    continue;
                }

                DB::table(
                    self::TABLE
                )->updateOrInsert(
                    [
                        'day_of_week' =>
                            $day,
                        'start_time' =>
                            $time
                            . ':00',
                    ],
                    [
                        'slot_number' =>
                            9999,
                        'updated_at' =>
                            now(),
                        'created_at' =>
                            now(),
                    ]
                );
            }
        }
    }

    private function reindexDay(
        int $day,
        bool $sync
    ): void {
        if (
            !Schema::hasTable(
                self::TABLE
            )
        ) {
            return;
        }

        $rows =
            DB::table(
                self::TABLE
            )
                ->where(
                    'day_of_week',
                    $day
                )
                ->orderBy(
                    'start_time'
                )
                ->orderBy(
                    'id'
                )
                ->get();

        $number = 1;

        foreach ($rows as $row) {
            DB::table(
                self::TABLE
            )
                ->where(
                    'id',
                    $row->id
                )
                ->update([
                    'slot_number' =>
                        $number,
                    'updated_at' =>
                        now(),
                ]);

            $number++;
        }

        if ($sync) {
            $this->synchronizeCodesForDay(
                $day
            );
        }
    }

    private function synchronizeCodesForDay(
        int $day
    ): void {
        $changes = [];

        /*
         * Étudiants.
         */
        if (
            Schema::hasTable(
                'class_user'
            )
            && Schema::hasColumn(
                'class_user',
                'student_slot_code'
            )
        ) {
            $rows =
                DB::table(
                    'class_user'
                )
                    ->join(
                        'subjects',
                        'class_user.subject_id',
                        '=',
                        'subjects.id'
                    )
                    ->join(
                        'class_rooms',
                        'class_user.class_id',
                        '=',
                        'class_rooms.id'
                    )
                    ->leftJoin(
                        'class_slots',
                        'class_user.class_slot_id',
                        '=',
                        'class_slots.id'
                    )
                    ->where(
                        'class_user.student_day_of_week',
                        $day
                    )
                    ->whereNotNull(
                        'class_user.student_start_time'
                    )
                    ->select([
                        'class_user.id',
                        'class_user.student_slot_code as old_code',
                        'class_user.student_start_time as start_time',
                        'subjects.name as subject_name',
                        'class_rooms.name as class_name',
                        'class_slots.code as group_code',
                    ])
                    ->get();

            foreach ($rows as $row) {
                $newCode =
                    $this->buildCode(
                        $day,
                        (string)
                            $row
                                ->start_time,
                        (string)
                            $row
                                ->subject_name,
                        (string)
                            $row
                                ->class_name,
                        (string) (
                            $row
                                ->group_code
                            ?? ''
                        )
                    );

                if (!$newCode) {
                    continue;
                }

                if (
                    $row->old_code
                    && $row->old_code
                        !== $newCode
                ) {
                    $changes[
                        (string)
                            $row
                                ->old_code
                    ] =
                        $newCode;
                }

                DB::table(
                    'class_user'
                )
                    ->where(
                        'id',
                        $row->id
                    )
                    ->update([
                        'student_slot_code' =>
                            $newCode,
                    ]);
            }
        }

        /*
         * Cours.
         */
        if (
            Schema::hasTable(
                'courses'
            )
            && Schema::hasColumn(
                'courses',
                'assignment_code'
            )
            && Schema::hasColumn(
                'courses',
                'assignment_day_of_week'
            )
            && Schema::hasColumn(
                'courses',
                'assignment_start_time'
            )
        ) {
            $rows =
                DB::table(
                    'courses'
                )
                    ->join(
                        'subjects',
                        'courses.subject_id',
                        '=',
                        'subjects.id'
                    )
                    ->join(
                        'class_rooms',
                        'courses.class_id',
                        '=',
                        'class_rooms.id'
                    )
                    ->where(
                        'courses.assignment_day_of_week',
                        $day
                    )
                    ->whereNotNull(
                        'courses.assignment_start_time'
                    )
                    ->select([
                        'courses.id',
                        'courses.assignment_code as old_code',
                        'courses.assignment_start_time as start_time',
                        'courses.slot_code as group_code',
                        'subjects.name as subject_name',
                        'class_rooms.name as class_name',
                    ])
                    ->get();

            foreach ($rows as $row) {
                $newCode =
                    $this->buildCode(
                        $day,
                        (string)
                            $row
                                ->start_time,
                        (string)
                            $row
                                ->subject_name,
                        (string)
                            $row
                                ->class_name,
                        (string) (
                            $row
                                ->group_code
                            ?? ''
                        )
                    );

                if (!$newCode) {
                    continue;
                }

                if (
                    $row->old_code
                    && $row->old_code
                        !== $newCode
                ) {
                    $changes[
                        (string)
                            $row
                                ->old_code
                    ] =
                        $newCode;
                }

                DB::table(
                    'courses'
                )
                    ->where(
                        'id',
                        $row->id
                    )
                    ->update([
                        'assignment_code' =>
                            $newCode,
                    ]);
            }
        }

        /*
         * Devoirs / copies.
         */
        if (
            Schema::hasTable(
                'assignments'
            )
            && Schema::hasColumn(
                'assignments',
                'assignment_code'
            )
            && Schema::hasColumn(
                'assignments',
                'assignment_day_of_week'
            )
            && Schema::hasColumn(
                'assignments',
                'assignment_start_time'
            )
        ) {
            $rows =
                DB::table(
                    'assignments'
                )
                    ->join(
                        'subjects',
                        'assignments.subject_id',
                        '=',
                        'subjects.id'
                    )
                    ->join(
                        'class_rooms',
                        'assignments.class_room_id',
                        '=',
                        'class_rooms.id'
                    )
                    ->leftJoin(
                        'class_slots',
                        'assignments.class_slot_id',
                        '=',
                        'class_slots.id'
                    )
                    ->where(
                        'assignments.assignment_day_of_week',
                        $day
                    )
                    ->whereNotNull(
                        'assignments.assignment_start_time'
                    )
                    ->select([
                        'assignments.id',
                        'assignments.assignment_code as old_code',
                        'assignments.assignment_start_time as start_time',
                        'subjects.name as subject_name',
                        'class_rooms.name as class_name',
                        'class_slots.code as group_code',
                    ])
                    ->get();

            foreach ($rows as $row) {
                $newCode =
                    $this->buildCode(
                        $day,
                        (string)
                            $row
                                ->start_time,
                        (string)
                            $row
                                ->subject_name,
                        (string)
                            $row
                                ->class_name,
                        (string) (
                            $row
                                ->group_code
                            ?? ''
                        )
                    );

                if (!$newCode) {
                    continue;
                }

                if (
                    $row->old_code
                    && $row->old_code
                        !== $newCode
                ) {
                    $changes[
                        (string)
                            $row
                                ->old_code
                    ] =
                        $newCode;
                }

                DB::table(
                    'assignments'
                )
                    ->where(
                        'id',
                        $row->id
                    )
                    ->update([
                        'assignment_code' =>
                            $newCode,
                    ]);
            }
        }

        /*
         * Chat : reprendre les changements de code connus.
         */
        if (
            !empty($changes)
            && Schema::hasTable(
                'messages'
            )
            && Schema::hasColumn(
                'messages',
                'assignment_code'
            )
        ) {
            foreach (
                $changes
                as $oldCode => $newCode
            ) {
                DB::table(
                    'messages'
                )
                    ->where(
                        'assignment_code',
                        $oldCode
                    )
                    ->update([
                        'assignment_code' =>
                            $newCode,
                    ]);
            }
        }
    }

    private function buildCode(
        int $day,
        string $time,
        string $subjectName,
        string $className,
        string $groupCode
    ): ?string {
        $number =
            $this->numberFor(
                $day,
                $time,
                false
            );

        if (!$number) {
            return null;
        }

        $dayCodes = [
            1 => 'L',
            2 => 'MA',
            3 => 'M',
            4 => 'J',
            5 => 'V',
            6 => 'S',
            7 => 'D',
        ];

        if (!isset($dayCodes[$day])) {
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
                                $subjectName
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

        $normalizedClass =
            strtolower(
                Str::ascii(
                    trim(
                        $className
                    )
                )
            );

        if (
            str_contains(
                $normalizedClass,
                'debut'
            )
        ) {
            $classCode = 'D';
        } elseif (
            str_contains(
                $normalizedClass,
                'inter'
            )
        ) {
            $classCode = 'I';
        } elseif (
            str_contains(
                $normalizedClass,
                'avance'
            )
            || str_contains(
                $normalizedClass,
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
                    $groupCode
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
            $dayCodes[$day]
            . $number
            . $subjectCode
            . $classCode
            . $groupNumber;
    }
}