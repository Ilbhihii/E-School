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
            || !Schema::hasColumn('class_user','student_slot_code')
            || !Schema::hasColumn('class_user','student_day_of_week')
            || !Schema::hasColumn('class_user','student_start_time')
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

        $slotStarts = [
            '08:00' => 1,
            '09:30' => 2,
            '11:00' => 3,
            '12:30' => 4,
            '14:00' => 5,
            '15:30' => 6,
            '17:00' => 7,
            '18:30' => 8,
            '20:00' => 9,
            '21:30' => 10,
        ];

        DB::table('class_user')
            ->join('subjects','class_user.subject_id','=','subjects.id')
            ->join('class_rooms','class_user.class_id','=','class_rooms.id')
            ->leftJoin('class_slots','class_user.class_slot_id','=','class_slots.id')
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
            ->chunk(200,function($rows) use ($dayCodes,$slotStarts) {
                foreach ($rows as $row) {
                    $day = (int) $row->student_day_of_week;
                    $start = substr((string) $row->student_start_time,0,5);

                    if (!isset($dayCodes[$day]) || !isset($slotStarts[$start])) {
                        continue;
                    }

                    $number = $slotStarts[$start];

                    $sn = preg_replace(
                        '/[^A-Z0-9]/',
                        '',
                        strtoupper(Str::ascii((string) $row->subject_name))
                    );
                    $subjectCode = substr((string) $sn,0,2);
                    if ($subjectCode === '') $subjectCode = 'MT';
                    elseif (strlen($subjectCode) === 1) $subjectCode .= 'X';

                    $cn = strtolower(Str::ascii(trim((string) $row->class_name)));

                    if (str_contains($cn,'debut')) $classCode = 'D';
                    elseif (str_contains($cn,'inter')) $classCode = 'I';
                    elseif (str_contains($cn,'avance')) $classCode = 'A';
                    else {
                        $simple = preg_replace(
                            '/[^A-Z0-9]/',
                            '',
                            strtoupper(Str::ascii((string) $row->class_name))
                        );
                        $classCode = substr((string) $simple,0,1) ?: 'X';
                    }

                    $gc = strtoupper(trim((string) $row->group_code));
                    if (preg_match('/(\d+)$/',$gc,$gm)) {
                        $groupNumber = $gm[1];
                    } else {
                        $groupNumber = preg_replace('/[^A-Z0-9]/','',$gc) ?: '1';
                    }

                    $newCode =
                        $dayCodes[$day]
                        . $number
                        . $subjectCode
                        . $classCode
                        . $groupNumber;

                    DB::table('class_user')
                        ->where('id',$row->id)
                        ->update([
                            'student_slot_code' => $newCode,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Pas de rollback automatique: plusieurs conventions historiques ont existé.
    }
};