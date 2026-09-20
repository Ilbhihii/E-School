<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('class_user')
            || !Schema::hasColumn(
                'class_user',
                'student_slot_code'
            )
            || !Schema::hasColumn(
                'class_user',
                'student_start_time'
            )
            || !Schema::hasColumn(
                'class_user',
                'student_end_time'
            )
        ) {
            return;
        }

        $slots = [
            1 => ['08:00:00', '09:30:00'],
            2 => ['09:30:00', '11:00:00'],
            3 => ['11:00:00', '12:30:00'],
            4 => ['12:30:00', '14:00:00'],
            5 => ['14:00:00', '15:30:00'],
            6 => ['15:30:00', '17:00:00'],
            7 => ['17:00:00', '18:30:00'],
            8 => ['18:30:00', '20:00:00'],
            9 => ['20:00:00', '21:30:00'],
            10 => ['21:30:00', '23:00:00'],
        ];

        DB::table('class_user')
            ->whereNotNull(
                'student_slot_code'
            )
            ->orderBy('id')
            ->chunkById(
                200,
                function ($rows) use ($slots) {
                    foreach ($rows as $row) {
                        $code = trim(
                            (string)
                                $row->student_slot_code
                        );

                        if (
                            !preg_match(
                                '/(\d+)$/',
                                $code,
                                $matches
                            )
                        ) {
                            continue;
                        }

                        $number =
                            (int) $matches[1];

                        if (!isset($slots[$number])) {
                            continue;
                        }

                        [
                            $start,
                            $end,
                        ] = $slots[$number];

                        DB::table('class_user')
                            ->where(
                                'id',
                                $row->id
                            )
                            ->update([
                                'student_start_time' =>
                                    $start,
                                'student_end_time' =>
                                    $end,
                            ]);
                    }
                },
                'id'
            );
    }

    public function down(): void
    {
        /*
         * Pas de restauration automatique des anciennes heures :
         * plusieurs grilles historiques ont existé.
         * Les sauvegardes de l'installateur permettent de revenir
         * au code précédent si nécessaire.
         */
    }
};