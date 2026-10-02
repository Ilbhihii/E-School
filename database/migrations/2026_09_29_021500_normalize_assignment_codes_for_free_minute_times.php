<?php

use App\Services\AssignmentScopeService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $scope =
            app(
                AssignmentScopeService::class
            );

        /*
         * Étudiants.
         */
        if (
            Schema::hasTable('class_user')
            && Schema::hasColumn(
                'class_user',
                'student_slot_code'
            )
        ) {
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
                        'class_slots',
                        'class_user.class_slot_id',
                        '=',
                        'class_slots.id'
                    )
                    ->whereNotNull(
                        'class_user.student_day_of_week'
                    )
                    ->whereNotNull(
                        'class_user.student_start_time'
                    )
                    ->select([
                        'class_user.id',
                        'class_user.student_day_of_week',
                        'class_user.student_start_time',
                        'subjects.name as subject_name',
                        'class_rooms.name as class_name',
                        'class_slots.code as group_code',
                    ])
                    ->get();

            foreach ($rows as $row) {
                $code =
                    $scope->build(
                        (int)
                            $row
                                ->student_day_of_week,
                        (string)
                            $row
                                ->student_start_time,
                        (string)
                            $row
                                ->subject_name,
                        '',
                        (string)
                            $row
                                ->class_name,
                        (string)
                            $row
                                ->group_code
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
                    ]);
            }
        }

        /*
         * Cours.
         */
        if (
            Schema::hasTable('courses')
            && Schema::hasColumn(
                'courses',
                'assignment_code'
            )
        ) {
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
                    ->whereNotNull(
                        'courses.assignment_day_of_week'
                    )
                    ->whereNotNull(
                        'courses.assignment_start_time'
                    )
                    ->whereNotNull(
                        'courses.slot_code'
                    )
                    ->select([
                        'courses.id',
                        'courses.assignment_day_of_week',
                        'courses.assignment_start_time',
                        'courses.slot_code',
                        'subjects.name as subject_name',
                        'class_rooms.name as class_name',
                    ])
                    ->get();

            foreach ($rows as $row) {
                $code =
                    $scope->build(
                        (int)
                            $row
                                ->assignment_day_of_week,
                        (string)
                            $row
                                ->assignment_start_time,
                        (string)
                            $row
                                ->subject_name,
                        '',
                        (string)
                            $row
                                ->class_name,
                        (string)
                            $row
                                ->slot_code
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
                    ]);
            }
        }

        /*
         * Devoirs / copies.
         */
        if (
            Schema::hasTable('assignments')
            && Schema::hasColumn(
                'assignments',
                'assignment_code'
            )
        ) {
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
                        'class_slots',
                        'assignments.class_slot_id',
                        '=',
                        'class_slots.id'
                    )
                    ->whereNotNull(
                        'assignments.assignment_day_of_week'
                    )
                    ->whereNotNull(
                        'assignments.assignment_start_time'
                    )
                    ->select([
                        'assignments.id',
                        'assignments.assignment_day_of_week',
                        'assignments.assignment_start_time',
                        'subjects.name as subject_name',
                        'class_rooms.name as class_name',
                        'class_slots.code as group_code',
                    ])
                    ->get();

            foreach ($rows as $row) {
                $code =
                    $scope->build(
                        (int)
                            $row
                                ->assignment_day_of_week,
                        (string)
                            $row
                                ->assignment_start_time,
                        (string)
                            $row
                                ->subject_name,
                        '',
                        (string)
                            $row
                                ->class_name,
                        (string)
                            $row
                                ->group_code
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
                    ]);
            }
        }
    }

    public function down(): void
    {
        /*
         * Pas de rollback automatique :
         * on ne veut pas deviner un ancien code lorsque l'heure
         * n'était pas sur la grille de 30 minutes.
         */
    }
};