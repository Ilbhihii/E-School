<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PedagogicalScopeCodeRebuildService
{
    public function rebuildAll(): void
    {
        for (
            $day = 1;
            $day <= 7;
            $day++
        ) {
            $this->rebuildDay(
                $day
            );
        }
    }

    public function rebuildDay(
        int $day
    ): void {
        if (
            $day < 1
            || $day > 7
        ) {
            return;
        }

        $timeNumbers =
            $this->timeNumbers(
                $day
            );

        if (
            $timeNumbers->isEmpty()
        ) {
            return;
        }

        $this->rebuildStudents(
            $day,
            $timeNumbers
        );

        $this->rebuildCourses(
            $day,
            $timeNumbers
        );

        $this->rebuildAssignments(
            $day,
            $timeNumbers
        );

        $this->rebuildMessages();
    }

    private function timeNumbers(
        int $day
    ): Collection {
        if (
            !Schema::hasTable(
                'pedagogical_time_slots'
            )
        ) {
            return collect();
        }

        return DB::table(
            'pedagogical_time_slots'
        )
            ->where(
                'day_of_week',
                $day
            )
            ->orderBy(
                'start_time'
            )
            ->get([
                'start_time',
                'slot_number',
            ])
            ->mapWithKeys(
                fn ($row) => [
                    substr(
                        (string) $row->start_time,
                        0,
                        5
                    ) =>
                        (int) $row->slot_number,
                ]
            );
    }

    private function rebuildStudents(
        int $day,
        Collection $timeNumbers
    ): void {
        if (
            !Schema::hasTable(
                'class_user'
            )
            || !Schema::hasColumn(
                'class_user',
                'student_slot_code'
            )
            || !Schema::hasColumn(
                'class_user',
                'student_day_of_week'
            )
            || !Schema::hasColumn(
                'class_user',
                'student_start_time'
            )
        ) {
            return;
        }

        $rows =
            DB::table('class_user')
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
                    'class_user.student_start_time',
                    'subjects.name as subject_name',
                    'class_rooms.name as class_name',
                    'class_slots.code as group_code',
                ])
                ->get();

        foreach ($rows as $row) {
            $time =
                substr(
                    (string) $row->student_start_time,
                    0,
                    5
                );

            $number =
                $timeNumbers->get(
                    $time
                );

            if (!$number) {
                continue;
            }

            $code =
                $this->buildCode(
                    $day,
                    (int) $number,
                    (string) $row->subject_name,
                    (string) $row->class_name,
                    (string) $row->group_code
                );

            if (!$code) {
                continue;
            }

            DB::table('class_user')
                ->where(
                    'id',
                    $row->id
                )
                ->update([
                    'student_slot_code' =>
                        $code,
                    'updated_at' =>
                        now(),
                ]);
        }
    }

    private function rebuildCourses(
        int $day,
        Collection $timeNumbers
    ): void {
        if (
            !Schema::hasTable(
                'courses'
            )
            || !Schema::hasColumn(
                'courses',
                'assignment_code'
            )
            || !Schema::hasColumn(
                'courses',
                'assignment_day_of_week'
            )
            || !Schema::hasColumn(
                'courses',
                'assignment_start_time'
            )
        ) {
            return;
        }

        $rows =
            DB::table('courses')
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
                ->whereNotNull(
                    'courses.slot_code'
                )
                ->select([
                    'courses.id',
                    'courses.assignment_start_time',
                    'courses.slot_code',
                    'subjects.name as subject_name',
                    'class_rooms.name as class_name',
                ])
                ->get();

        foreach ($rows as $row) {
            $time =
                substr(
                    (string) $row->assignment_start_time,
                    0,
                    5
                );

            $number =
                $timeNumbers->get(
                    $time
                );

            if (!$number) {
                continue;
            }

            $code =
                $this->buildCode(
                    $day,
                    (int) $number,
                    (string) $row->subject_name,
                    (string) $row->class_name,
                    (string) $row->slot_code
                );

            if (!$code) {
                continue;
            }

            DB::table('courses')
                ->where(
                    'id',
                    $row->id
                )
                ->update([
                    'assignment_code' =>
                        $code,
                    'updated_at' =>
                        now(),
                ]);
        }
    }

    private function rebuildAssignments(
        int $day,
        Collection $timeNumbers
    ): void {
        if (
            !Schema::hasTable(
                'assignments'
            )
            || !Schema::hasColumn(
                'assignments',
                'assignment_code'
            )
            || !Schema::hasColumn(
                'assignments',
                'assignment_day_of_week'
            )
            || !Schema::hasColumn(
                'assignments',
                'assignment_start_time'
            )
        ) {
            return;
        }

        $rows =
            DB::table('assignments')
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
                ->whereNotNull(
                    'assignments.class_slot_id'
                )
                ->select([
                    'assignments.id',
                    'assignments.assignment_start_time',
                    'subjects.name as subject_name',
                    'class_rooms.name as class_name',
                    'class_slots.code as group_code',
                ])
                ->get();

        foreach ($rows as $row) {
            $time =
                substr(
                    (string) $row->assignment_start_time,
                    0,
                    5
                );

            $number =
                $timeNumbers->get(
                    $time
                );

            if (!$number) {
                continue;
            }

            $code =
                $this->buildCode(
                    $day,
                    (int) $number,
                    (string) $row->subject_name,
                    (string) $row->class_name,
                    (string) $row->group_code
                );

            if (!$code) {
                continue;
            }

            DB::table('assignments')
                ->where(
                    'id',
                    $row->id
                )
                ->update([
                    'assignment_code' =>
                        $code,
                    'updated_at' =>
                        now(),
                ]);
        }
    }

    /**
     * Les messages n'ont pas leur propre jour/heure.
     *
     * On les recalcule seulement lorsqu'un UNIQUE scope jour+heure
     * peut être déduit pour matière + groupe depuis class_user ou
     * prof_assignments.
     *
     * Si le scope est ambigu, le message n'est pas modifié.
     */
    private function rebuildMessages(): void
    {
        if (
            !Schema::hasTable(
                'messages'
            )
            || !Schema::hasColumn(
                'messages',
                'assignment_code'
            )
            || !Schema::hasColumn(
                'messages',
                'class_slot_id'
            )
        ) {
            return;
        }

        $messages =
            DB::table('messages')
                ->whereNotNull(
                    'class_slot_id'
                )
                ->select([
                    'id',
                    'subject_id',
                    'class_slot_id',
                ])
                ->get();

        foreach ($messages as $message) {
            $scopes =
                collect();

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
                $studentScopes =
                    DB::table(
                        'class_user'
                    )
                        ->where(
                            'subject_id',
                            $message->subject_id
                        )
                        ->where(
                            'class_slot_id',
                            $message->class_slot_id
                        )
                        ->whereNotNull(
                            'student_day_of_week'
                        )
                        ->whereNotNull(
                            'student_start_time'
                        )
                        ->get([
                            'student_day_of_week as day',
                            'student_start_time as start_time',
                            'class_id',
                        ]);

                $scopes =
                    $scopes->merge(
                        $studentScopes
                    );
            }

            if (
                Schema::hasTable(
                    'prof_assignments'
                )
            ) {
                $profScopes =
                    DB::table(
                        'prof_assignments'
                    )
                        ->where(
                            'subject_id',
                            $message->subject_id
                        )
                        ->where(
                            'class_slot_id',
                            $message->class_slot_id
                        )
                        ->whereNotNull(
                            'day_of_week'
                        )
                        ->whereNotNull(
                            'start_time'
                        )
                        ->get([
                            'day_of_week as day',
                            'start_time',
                            'class_id',
                        ]);

                $scopes =
                    $scopes->merge(
                        $profScopes
                    );
            }

            $scopes =
                $scopes
                    ->map(
                        fn ($scope) => (object) [
                            'day' =>
                                (int) $scope->day,
                            'start_time' =>
                                substr(
                                    (string) $scope->start_time,
                                    0,
                                    5
                                ),
                            'class_id' =>
                                (int) $scope->class_id,
                        ]
                    )
                    ->unique(
                        fn ($scope) =>
                            $scope->day
                            . '|'
                            . $scope->start_time
                            . '|'
                            . $scope->class_id
                    )
                    ->values();

            if (
                $scopes->count()
                !== 1
            ) {
                continue;
            }

            $scope =
                $scopes->first();

            $classRoom =
                DB::table(
                    'class_rooms'
                )
                    ->where(
                        'id',
                        $scope->class_id
                    )
                    ->first();

            $subject =
                DB::table(
                    'subjects'
                )
                    ->where(
                        'id',
                        $message->subject_id
                    )
                    ->first();

            $classSlot =
                DB::table(
                    'class_slots'
                )
                    ->where(
                        'id',
                        $message->class_slot_id
                    )
                    ->first();

            if (
                !$classRoom
                || !$subject
                || !$classSlot
            ) {
                continue;
            }

            $number =
                DB::table(
                    'pedagogical_time_slots'
                )
                    ->where(
                        'day_of_week',
                        $scope->day
                    )
                    ->whereTime(
                        'start_time',
                        '=',
                        $scope->start_time
                    )
                    ->value(
                        'slot_number'
                    );

            if (!$number) {
                continue;
            }

            $code =
                $this->buildCode(
                    $scope->day,
                    (int) $number,
                    (string) $subject->name,
                    (string) $classRoom->name,
                    (string) $classSlot->code
                );

            if (!$code) {
                continue;
            }

            DB::table('messages')
                ->where(
                    'id',
                    $message->id
                )
                ->update([
                    'assignment_code' =>
                        $code,
                    'updated_at' =>
                        now(),
                ]);
        }
    }

    private function buildCode(
        int $day,
        int $number,
        string $subjectName,
        string $className,
        string $groupCode
    ): ?string {
        $dayCodes = [
            1 => 'L',
            2 => 'MA',
            3 => 'M',
            4 => 'J',
            5 => 'V',
            6 => 'S',
            7 => 'D',
        ];

        if (
            !isset(
                $dayCodes[$day]
            )
            || $number < 1
        ) {
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
            $simpleClass =
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
                );

            $classCode =
                substr(
                    (string) $simpleClass,
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
            $matches
        );

        $groupNumber =
            $matches[1]
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