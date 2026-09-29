<?php

use App\Models\ProfAssignment;
use App\Services\AssignmentScopeService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'assignment_code')) {
                    $table
                        ->string('assignment_code', 64)
                        ->nullable()
                        ->index();
                }

                if (!Schema::hasColumn('messages', 'class_slot_id')) {
                    $table
                        ->unsignedBigInteger('class_slot_id')
                        ->nullable()
                        ->index();
                }
            });
        }

        /*
         * Reprise des anciens COURS :
         * uniquement si une affectation professeur unique correspond
         * exactement à matière + niveau + classe + groupe.
         */
        if (
            Schema::hasTable('courses')
            && Schema::hasColumn('courses', 'assignment_code')
        ) {
            DB::table('courses')
                ->whereNull('assignment_code')
                ->whereNotNull('user_id')
                ->orderBy('id')
                ->chunkById(
                    100,
                    function ($rows) {
                        foreach ($rows as $row) {
                            $query =
                                ProfAssignment::query()
                                    ->with('classSlot')
                                    ->where(
                                        'prof_id',
                                        (int) $row->user_id
                                    )
                                    ->where(
                                        'subject_id',
                                        (int) $row->subject_id
                                    )
                                    ->where(
                                        'level_id',
                                        (int) $row->level_id
                                    )
                                    ->where(
                                        'class_id',
                                        (int) $row->class_id
                                    );

                            if (
                                property_exists($row, 'slot_code')
                                && trim(
                                    (string) $row->slot_code
                                ) !== ''
                            ) {
                                $wanted =
                                    strtoupper(
                                        trim(
                                            (string) $row->slot_code
                                        )
                                    );

                                $matches =
                                    $query
                                        ->get()
                                        ->filter(
                                            fn ($assignment) =>
                                                strtoupper(
                                                    trim(
                                                        (string)
                                                            $assignment
                                                                ->classSlot
                                                                ?->code
                                                    )
                                                ) === $wanted
                                        )
                                        ->values();
                            } else {
                                $matches =
                                    $query
                                        ->get()
                                        ->values();
                            }

                            if ($matches->count() !== 1) {
                                continue;
                            }

                            $assignment =
                                $matches->first();

                            $code =
                                app(
                                    AssignmentScopeService::class
                                )->professorCode(
                                    $assignment
                                );

                            if (!$code) {
                                continue;
                            }

                            DB::table('courses')
                                ->where('id', $row->id)
                                ->update([
                                    'assignment_code' =>
                                        $code,
                                    'assignment_day_of_week' =>
                                        $assignment->day_of_week,
                                    'assignment_start_time' =>
                                        $assignment->start_time,
                                ]);
                        }
                    }
                );
        }

        /*
         * Reprise des anciens DEVOIRS et COPIES :
         * - professeur : via ProfAssignment unique ;
         * - étudiant : via class_user exact.
         */
        if (
            Schema::hasTable('assignments')
            && Schema::hasColumn(
                'assignments',
                'assignment_code'
            )
        ) {
            DB::table('assignments')
                ->whereNull('assignment_code')
                ->orderBy('id')
                ->chunkById(
                    100,
                    function ($rows) {
                        foreach ($rows as $row) {
                            $role =
                                DB::table('users')
                                    ->where(
                                        'id',
                                        $row->user_id
                                    )
                                    ->value('role');

                            if ($role === 'prof') {
                                $classRoom =
                                    DB::table(
                                        'class_rooms'
                                    )
                                        ->where(
                                            'id',
                                            $row
                                                ->class_room_id
                                        )
                                        ->first();

                                if (!$classRoom) {
                                    continue;
                                }

                                $query =
                                    ProfAssignment::query()
                                        ->where(
                                            'prof_id',
                                            (int) $row->user_id
                                        )
                                        ->where(
                                            'subject_id',
                                            (int) $row->subject_id
                                        )
                                        ->where(
                                            'level_id',
                                            (int) $classRoom->level_id
                                        )
                                        ->where(
                                            'class_id',
                                            (int) $row->class_room_id
                                        );

                                if (
                                    !empty(
                                        $row->class_slot_id
                                    )
                                ) {
                                    $query->where(
                                        'class_slot_id',
                                        (int) $row
                                            ->class_slot_id
                                    );
                                }

                                $matches =
                                    $query->get();

                                if (
                                    $matches->count()
                                    !== 1
                                ) {
                                    continue;
                                }

                                $scope =
                                    $matches->first();

                                $code =
                                    app(
                                        AssignmentScopeService::class
                                    )->professorCode(
                                        $scope
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
                                        'assignment_day_of_week' =>
                                            $scope
                                                ->day_of_week,
                                        'assignment_start_time' =>
                                            $scope
                                                ->start_time,
                                    ]);

                                continue;
                            }

                            if ($role !== 'student') {
                                continue;
                            }

                            if (
                                !Schema::hasTable(
                                    'class_user'
                                )
                                || !Schema::hasColumn(
                                    'class_user',
                                    'student_slot_code'
                                )
                            ) {
                                continue;
                            }

                            $query =
                                DB::table('class_user')
                                    ->where(
                                        'user_id',
                                        $row->user_id
                                    )
                                    ->where(
                                        'subject_id',
                                        $row->subject_id
                                    )
                                    ->where(
                                        'class_id',
                                        $row->class_room_id
                                    );

                            if (
                                !empty(
                                    $row->class_slot_id
                                )
                                && Schema::hasColumn(
                                    'class_user',
                                    'class_slot_id'
                                )
                            ) {
                                $query->where(
                                    'class_slot_id',
                                    $row->class_slot_id
                                );
                            }

                            $matches =
                                $query->get();

                            if (
                                $matches->count()
                                !== 1
                            ) {
                                continue;
                            }

                            $scope =
                                $matches->first();

                            if (
                                empty(
                                    $scope
                                        ->student_slot_code
                                )
                            ) {
                                continue;
                            }

                            DB::table('assignments')
                                ->where(
                                    'id',
                                    $row->id
                                )
                                ->update([
                                    'assignment_code' =>
                                        $scope
                                            ->student_slot_code,
                                    'assignment_day_of_week' =>
                                        $scope
                                            ->student_day_of_week
                                        ?? null,
                                    'assignment_start_time' =>
                                        $scope
                                            ->student_start_time
                                        ?? null,
                                ]);
                        }
                    }
                );
        }

        /*
         * Les anciens messages de matière restent sans assignment_code :
         * on ne peut pas déterminer leur groupe sans risque.
         */
    }

    public function down(): void
    {
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                foreach (
                    [
                        'assignment_code',
                        'class_slot_id',
                    ]
                    as $column
                ) {
                    if (
                        Schema::hasColumn(
                            'messages',
                            $column
                        )
                    ) {
                        $table->dropColumn(
                            $column
                        );
                    }
                }
            });
        }
    }
};