<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('class_user')
            || !Schema::hasColumn('class_user', 'student_slot_code')
            || !Schema::hasColumn('class_user', 'student_day_of_week')
            || !Schema::hasColumn('class_user', 'student_start_time')
        ) {
            return;
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

        DB::table('class_user')
            ->join('subjects', 'class_user.subject_id', '=', 'subjects.id')
            ->join('class_rooms', 'class_user.class_id', '=', 'class_rooms.id')
            ->leftJoin('class_slots', 'class_user.class_slot_id', '=', 'class_slots.id')
            ->whereNotNull('class_user.student_day_of_week')
            ->whereNotNull('class_user.student_start_time')
            ->select([
                'class_user.id',
                'class_user.student_day_of_week',
                'class_user.student_start_time',
                'subjects.name as subject_name',
                'class_rooms.name as class_name',
                'class_slots.code as group_code',
            ])
            ->orderBy('class_user.id')
            ->chunk(200, function ($rows) use ($dayCodes) {
                foreach ($rows as $row) {
                    $day = (int) $row->student_day_of_week;

                    if (!isset($dayCodes[$day])) {
                        continue;
                    }

                    $start = substr((string) $row->student_start_time, 0, 5);
                    [$hour, $minute] = array_map('intval', explode(':', $start));

                    $minutes = ($hour * 60) + $minute;
                    $first = 8 * 60;
                    $last = 22 * 60;

                    if (
                        $minutes < $first
                        || $minutes > $last
                        || (($minutes - $first) % 30) !== 0
                    ) {
                        continue;
                    }

                    $slotNumber = intdiv($minutes - $first, 30) + 1;

                    $subjectNormalized = preg_replace(
                        '/[^A-Z0-9]/',
                        '',
                        strtoupper(Str::ascii((string) $row->subject_name))
                    );

                    $subjectCode = substr((string) $subjectNormalized, 0, 2);

                    if ($subjectCode === '') {
                        $subjectCode = 'MT';
                    } elseif (strlen($subjectCode) === 1) {
                        $subjectCode .= 'X';
                    }

                    $normalizedClass = strtolower(
                        Str::ascii(trim((string) $row->class_name))
                    );

                    if (str_contains($normalizedClass, 'debut')) {
                        $classCode = 'D';
                    } elseif (str_contains($normalizedClass, 'inter')) {
                        $classCode = 'I';
                    } elseif (str_contains($normalizedClass, 'avance')) {
                        $classCode = 'A';
                    } else {
                        $simple = preg_replace(
                            '/[^A-Z0-9]/',
                            '',
                            strtoupper(Str::ascii((string) $row->class_name))
                        );

                        $classCode = substr((string) $simple, 0, 1) ?: 'X';
                    }

                    $groupCode = strtoupper(trim((string) $row->group_code));

                    if (preg_match('/(\d+)$/', $groupCode, $groupMatch)) {
                        $groupNumber = $groupMatch[1];
                    } else {
                        $groupNumber = preg_replace(
                            '/[^A-Z0-9]/',
                            '',
                            $groupCode
                        ) ?: '1';
                    }

                    $newCode =
                        $dayCodes[$day]
                        . $slotNumber
                        . $subjectCode
                        . $classCode
                        . $groupNumber;

                    DB::table('class_user')
                        ->where('id', $row->id)
                        ->update([
                            'student_slot_code' => $newCode,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Pas de rollback automatique : plusieurs conventions historiques existent.
    }
};