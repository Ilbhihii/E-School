<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pedagogical_time_slots')) {
            Schema::create(
                'pedagogical_time_slots',
                function (Blueprint $table) {
                    $table->id();
                    $table->unsignedTinyInteger('day_of_week');
                    $table->time('start_time');
                    $table->unsignedSmallInteger('slot_number');
                    $table->timestamps();

                    $table->unique(
                        ['day_of_week', 'start_time'],
                        'ped_time_slots_day_time_unique'
                    );

                    $table->unique(
                        ['day_of_week', 'slot_number'],
                        'ped_time_slots_day_number_unique'
                    );
                }
            );
        }

        if (Schema::hasTable('lives')) {
            Schema::table(
                'lives',
                function (Blueprint $table) {
                    if (
                        !Schema::hasColumn(
                            'lives',
                            'assignment_day_of_week'
                        )
                    ) {
                        $table
                            ->unsignedTinyInteger(
                                'assignment_day_of_week'
                            )
                            ->nullable()
                            ->index();
                    }

                    if (
                        !Schema::hasColumn(
                            'lives',
                            'assignment_start_time'
                        )
                    ) {
                        $table
                            ->time('assignment_start_time')
                            ->nullable();
                    }

                    if (
                        !Schema::hasColumn(
                            'lives',
                            'assignment_end_time'
                        )
                    ) {
                        $table
                            ->time('assignment_end_time')
                            ->nullable();
                    }
                }
            );
        }

        /*
         * Initialiser le registre avec les heures déjà présentes.
         *
         * Important :
         * - 08:00 est toujours le n°1 ;
         * - les autres heures existantes sont triées lors de cette
         *   première initialisation.
         * - ensuite, le numéro reste stable.
         */
        for ($day = 1; $day <= 7; $day++) {
            $times = ['08:00'];

            if (
                Schema::hasTable('class_user')
                && Schema::hasColumn(
                    'class_user',
                    'student_day_of_week'
                )
                && Schema::hasColumn(
                    'class_user',
                    'student_start_time'
                )
            ) {
                $values =
                    DB::table('class_user')
                        ->where(
                            'student_day_of_week',
                            $day
                        )
                        ->whereNotNull(
                            'student_start_time'
                        )
                        ->pluck(
                            'student_start_time'
                        );

                foreach ($values as $value) {
                    $times[] =
                        substr(
                            (string) $value,
                            0,
                            5
                        );
                }
            }

            if (
                Schema::hasTable('prof_assignments')
                && Schema::hasColumn(
                    'prof_assignments',
                    'day_of_week'
                )
                && Schema::hasColumn(
                    'prof_assignments',
                    'start_time'
                )
            ) {
                $values =
                    DB::table('prof_assignments')
                        ->where(
                            'day_of_week',
                            $day
                        )
                        ->whereNotNull(
                            'start_time'
                        )
                        ->pluck(
                            'start_time'
                        );

                foreach ($values as $value) {
                    $times[] =
                        substr(
                            (string) $value,
                            0,
                            5
                        );
                }
            }

            if (
                Schema::hasTable('schedules')
                && Schema::hasColumn(
                    'schedules',
                    'day_of_week'
                )
                && Schema::hasColumn(
                    'schedules',
                    'start_time'
                )
            ) {
                $values =
                    DB::table('schedules')
                        ->where(
                            'day_of_week',
                            $day
                        )
                        ->whereNotNull(
                            'start_time'
                        )
                        ->pluck(
                            'start_time'
                        );

                foreach ($values as $value) {
                    $times[] =
                        substr(
                            (string) $value,
                            0,
                            5
                        );
                }
            }

            if (
                Schema::hasTable('lives')
                && Schema::hasColumn(
                    'lives',
                    'live_date'
                )
                && Schema::hasColumn(
                    'lives',
                    'start_time'
                )
            ) {
                $lives =
                    DB::table('lives')
                        ->whereNotNull('live_date')
                        ->whereNotNull('start_time')
                        ->get([
                            'live_date',
                            'start_time',
                        ]);

                foreach ($lives as $live) {
                    try {
                        $liveDay =
                            Carbon::parse(
                                $live->live_date
                            )->dayOfWeekIso;
                    } catch (\Throwable $exception) {
                        continue;
                    }

                    if ((int) $liveDay !== $day) {
                        continue;
                    }

                    $times[] =
                        substr(
                            (string) $live->start_time,
                            0,
                            5
                        );
                }
            }

            $times =
                collect($times)
                    ->filter(
                        fn ($time) =>
                            is_string($time)
                            && preg_match(
                                '/^\d{2}:\d{2}$/',
                                $time
                            )
                            && $time >= '08:00'
                            && $time <= '22:00'
                    )
                    ->unique()
                    ->sort()
                    ->values();

            /*
             * Forcer 08:00 en première position.
             */
            $times =
                collect(['08:00'])
                    ->merge(
                        $times->reject(
                            fn ($time) =>
                                $time === '08:00'
                        )
                    )
                    ->values();

            foreach ($times as $index => $time) {
                DB::table(
                    'pedagogical_time_slots'
                )->updateOrInsert(
                    [
                        'day_of_week' => $day,
                        'start_time' =>
                            $time . ':00',
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
    }

    public function down(): void
    {
        if (Schema::hasTable('lives')) {
            Schema::table(
                'lives',
                function (Blueprint $table) {
                    foreach (
                        [
                            'assignment_day_of_week',
                            'assignment_start_time',
                            'assignment_end_time',
                        ]
                        as $column
                    ) {
                        if (
                            Schema::hasColumn(
                                'lives',
                                $column
                            )
                        ) {
                            $table->dropColumn(
                                $column
                            );
                        }
                    }
                }
            );
        }

        Schema::dropIfExists(
            'pedagogical_time_slots'
        );
    }
};