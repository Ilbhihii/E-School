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

        $this->rebuildProfessors(
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
            !Schema::hasTable('class_user')
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
                ->join(
                    'levels',
                    'class_rooms.level_id',
                    '=',
                    'levels.id'
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
                    'class_user.subject_id',
                    'class_user.class_id',
                    'class_user.student_start_time',
                    'class_rooms.level_id',
                    'subjects.name as subject_name',
                    'levels.name as level_name',
                    'class_rooms.name as class_name',
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

            $slot =
                $this->ensureGroupSlot(
                    (int) $row->subject_id,
                    (int) $row->level_id,
                    (int) $row->class_id,
                    (string) $row->class_name,
                    (int) $number
                );

            if (!$slot) {
                continue;
            }

            $code =
                $this->buildCode(
                    $day,
                    (int) $number,
                    (string) $row->subject_name,
                    (string) $row->level_name,
                    (string) $slot->code
                );

            if (!$code) {
                continue;
            }

            DB::table('class_user')
                ->where('id', $row->id)
                ->update([
                    'class_slot_id' =>
                        (int) $slot->id,
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
            !Schema::hasTable('courses')
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
                ->join(
                    'levels',
                    'class_rooms.level_id',
                    '=',
                    'levels.id'
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
                    'courses.subject_id',
                    'courses.class_id',
                    'courses.assignment_start_time',
                    'class_rooms.level_id',
                    'subjects.name as subject_name',
                    'levels.name as level_name',
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

            $slot =
                $this->ensureGroupSlot(
                    (int) $row->subject_id,
                    (int) $row->level_id,
                    (int) $row->class_id,
                    (string) $row->class_name,
                    (int) $number
                );

            if (!$slot) {
                continue;
            }

            $code =
                $this->buildCode(
                    $day,
                    (int) $number,
                    (string) $row->subject_name,
                    (string) $row->level_name,
                    (string) $slot->code
                );

            if (!$code) {
                continue;
            }

            $values = [
                'assignment_code' =>
                    $code,
                'updated_at' =>
                    now(),
            ];

            if (
                Schema::hasColumn(
                    'courses',
                    'slot_code'
                )
            ) {
                $values['slot_code'] =
                    (string) $slot->code;
            }

            DB::table('courses')
                ->where('id', $row->id)
                ->update($values);
        }
    }

    private function rebuildAssignments(
        int $day,
        Collection $timeNumbers
    ): void {
        if (
            !Schema::hasTable('assignments')
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
                ->join(
                    'levels',
                    'class_rooms.level_id',
                    '=',
                    'levels.id'
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
                    'assignments.subject_id',
                    'assignments.class_room_id as class_id',
                    'assignments.assignment_start_time',
                    'class_rooms.level_id',
                    'subjects.name as subject_name',
                    'levels.name as level_name',
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

            $slot =
                $this->ensureGroupSlot(
                    (int) $row->subject_id,
                    (int) $row->level_id,
                    (int) $row->class_id,
                    (string) $row->class_name,
                    (int) $number
                );

            if (!$slot) {
                continue;
            }

            $code =
                $this->buildCode(
                    $day,
                    (int) $number,
                    (string) $row->subject_name,
                    (string) $row->level_name,
                    (string) $slot->code
                );

            if (!$code) {
                continue;
            }

            $values = [
                'assignment_code' =>
                    $code,
                'updated_at' =>
                    now(),
            ];

            if (
                Schema::hasColumn(
                    'assignments',
                    'class_slot_id'
                )
            ) {
                $values['class_slot_id'] =
                    (int) $slot->id;
            }

            DB::table('assignments')
                ->where('id', $row->id)
                ->update($values);
        }
    }

    /**
     * Recalcule également le groupe des professeurs lorsqu'une nouvelle
     * heure est insérée dans la journée.
     *
     * Exemple : si 08:45 est ajouté entre 08:30 et 09:00, un professeur
     * placé à 09:00 passe automatiquement de D3 à D4, exactement comme
     * les étudiants.
     */
    private function rebuildProfessors(
        int $day,
        Collection $timeNumbers
    ): void {
        if (
            !Schema::hasTable('prof_assignments')
            || !Schema::hasColumn(
                'prof_assignments',
                'class_slot_id'
            )
            || !Schema::hasColumn(
                'prof_assignments',
                'day_of_week'
            )
            || !Schema::hasColumn(
                'prof_assignments',
                'start_time'
            )
        ) {
            return;
        }

        $rows =
            DB::table('prof_assignments')
                ->join(
                    'subjects',
                    'prof_assignments.subject_id',
                    '=',
                    'subjects.id'
                )
                ->join(
                    'class_rooms',
                    'prof_assignments.class_id',
                    '=',
                    'class_rooms.id'
                )
                ->join(
                    'levels',
                    'prof_assignments.level_id',
                    '=',
                    'levels.id'
                )
                ->where(
                    'prof_assignments.day_of_week',
                    $day
                )
                ->whereNotNull(
                    'prof_assignments.start_time'
                )
                ->select([
                    'prof_assignments.id',
                    'prof_assignments.subject_id',
                    'prof_assignments.level_id',
                    'prof_assignments.class_id',
                    'prof_assignments.start_time',
                    'class_rooms.name as class_name',
                ])
                ->get();

        if ($rows->isEmpty()) {
            return;
        }

        DB::transaction(
            function () use (
                $rows,
                $timeNumbers
            ) {
                /*
                 * class_slot_id fait partie d'un index unique. On libère
                 * temporairement les groupes afin d'éviter un conflit
                 * transitoire D3 -> D4 pendant que D4 -> D5 n'a pas encore
                 * été appliqué.
                 */
                DB::table('prof_assignments')
                    ->whereIn(
                        'id',
                        $rows->pluck('id')
                    )
                    ->update([
                        'class_slot_id' => null,
                        'updated_at' => now(),
                    ]);

                foreach ($rows as $row) {
                    $time =
                        substr(
                            (string) $row->start_time,
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

                    $slot =
                        $this->ensureGroupSlot(
                            (int) $row->subject_id,
                            (int) $row->level_id,
                            (int) $row->class_id,
                            (string) $row->class_name,
                            (int) $number
                        );

                    if (!$slot) {
                        continue;
                    }

                    DB::table('prof_assignments')
                        ->where(
                            'id',
                            $row->id
                        )
                        ->update([
                            'class_slot_id' =>
                                (int) $slot->id,
                            'updated_at' =>
                                now(),
                        ]);
                }
            }
        );
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

            $level =
                $classRoom
                    ? DB::table('levels')
                        ->where(
                            'id',
                            $classRoom->level_id
                        )
                        ->first()
                    : null;

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
                || !$level
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
                    (string) $level->name,
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

    /**
     * Crée/retourne le groupe dynamique correspondant au rang horaire.
     */
    private function ensureGroupSlot(
        int $subjectId,
        int $levelId,
        int $classId,
        string $className,
        int $number
    ): ?object {
        if (
            $subjectId < 1
            || $levelId < 1
            || $classId < 1
            || $number < 1
            || !Schema::hasTable(
                'class_slots'
            )
        ) {
            return null;
        }

        $prefix =
            $this->classPrefix(
                $className
            );

        $code =
            $prefix
            . $number;

        $existing = DB::table('class_slots')
            ->where('subject_id', $subjectId)
            ->where('level_id', $levelId)
            ->where('class_id', $classId)
            ->where('code', $code)
            ->first();

        if ($existing) {
            DB::table('class_slots')
                ->where('id', $existing->id)
                ->update([
                    'position' => $number,
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('class_slots')
                ->insert([
                    'subject_id' => $subjectId,
                    'level_id' => $levelId,
                    'class_id' => $classId,
                    'code' => $code,
                    'position' => $number,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        return DB::table('class_slots')
            ->where(
                'subject_id',
                $subjectId
            )
            ->where(
                'level_id',
                $levelId
            )
            ->where(
                'class_id',
                $classId
            )
            ->where(
                'code',
                $code
            )
            ->first();
    }

    /**
     * Format :
     * [Jour]1[Matière][Niveau][Groupe]
     *
     * Exemple :
     * D1ARLED3
     * = Dimanche + Arabe + Lecture & Écriture + groupe D3.
     */
    private function buildCode(
        int $day,
        int $number,
        string $subjectName,
        string $levelName,
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
            $this->twoLetterCode(
                $subjectName,
                'MT'
            );

        $levelCode =
            $this->twoLetterCode(
                $levelName,
                'NV'
            );

        $groupCode =
            strtoupper(
                trim(
                    $groupCode
                )
            );

        if ($groupCode === '') {
            return null;
        }

        return
            $dayCodes[$day]
            . '1'
            . $subjectCode
            . $levelCode
            . $groupCode;
    }

    private function classPrefix(
        string $className
    ): string {
        $normalized =
            strtolower(
                Str::ascii(
                    trim(
                        $className
                    )
                )
            );

        if (
            str_contains(
                $normalized,
                'debut'
            )
        ) {
            return 'D';
        }

        if (
            str_contains(
                $normalized,
                'inter'
            )
        ) {
            return 'I';
        }

        if (
            str_contains(
                $normalized,
                'avance'
            )
            || str_contains(
                $normalized,
                'adulte'
            )
        ) {
            return 'A';
        }

        $simple =
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

        return
            substr(
                (string) $simple,
                0,
                1
            )
            ?: 'G';
    }

    private function twoLetterCode(
        string $value,
        string $fallback
    ): string {
        $normalized =
            preg_replace(
                '/[^A-Z0-9]/',
                '',
                strtoupper(
                    Str::ascii(
                        trim(
                            $value
                        )
                    )
                )
            );

        $code =
            substr(
                (string) $normalized,
                0,
                2
            );

        if ($code === '') {
            return $fallback;
        }

        return strlen($code) === 1
            ? $code . 'X'
            : $code;
    }
}