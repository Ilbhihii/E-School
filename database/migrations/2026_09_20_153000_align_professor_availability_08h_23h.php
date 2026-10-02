<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable(
                'professor_availabilities'
            )
        ) {
            return;
        }

        $map = [
            '09:00-10:30' => ['08:00:00', '09:30:00'],
            '10:30-12:00' => ['09:30:00', '11:00:00'],
            '12:00-13:30' => ['11:00:00', '12:30:00'],
            '13:30-15:00' => ['12:30:00', '14:00:00'],
            '15:00-16:30' => ['14:00:00', '15:30:00'],
            '16:30-18:00' => ['15:30:00', '17:00:00'],
            '18:00-19:30' => ['17:00:00', '18:30:00'],
            '19:30-21:00' => ['18:30:00', '20:00:00'],
            '21:00-22:00' => ['20:00:00', '21:30:00'],
        ];

        DB::table(
            'professor_availabilities'
        )
            ->orderBy('id')
            ->chunkById(
                200,
                function ($rows) use ($map) {
                    foreach ($rows as $row) {
                        $start = substr(
                            (string) $row->start_time,
                            0,
                            5
                        );

                        $end = substr(
                            (string) $row->end_time,
                            0,
                            5
                        );

                        $key =
                            $start
                            . '-'
                            . $end;

                        if (!isset($map[$key])) {
                            continue;
                        }

                        [
                            $newStart,
                            $newEnd,
                        ] = $map[$key];

                        /*
                         * Si une disponibilité identique dans la
                         * nouvelle grille existe déjà pour ce prof/jour,
                         * on supprime l'ancienne ligne au lieu de créer
                         * un doublon.
                         */
                        $duplicate = DB::table(
                            'professor_availabilities'
                        )
                            ->where(
                                'prof_id',
                                $row->prof_id
                            )
                            ->where(
                                'day_of_week',
                                $row->day_of_week
                            )
                            ->where(
                                'start_time',
                                $newStart
                            )
                            ->where(
                                'end_time',
                                $newEnd
                            )
                            ->where(
                                'id',
                                '!=',
                                $row->id
                            )
                            ->exists();

                        if ($duplicate) {
                            DB::table(
                                'professor_availabilities'
                            )
                                ->where(
                                    'id',
                                    $row->id
                                )
                                ->delete();

                            continue;
                        }

                        DB::table(
                            'professor_availabilities'
                        )
                            ->where(
                                'id',
                                $row->id
                            )
                            ->update([
                                'start_time' =>
                                    $newStart,
                                'end_time' =>
                                    $newEnd,
                                'updated_at' =>
                                    now(),
                            ]);
                    }
                },
                'id'
            );
    }

    public function down(): void
    {
        if (
            !Schema::hasTable(
                'professor_availabilities'
            )
        ) {
            return;
        }

        $map = [
            '08:00-09:30' => ['09:00:00', '10:30:00'],
            '09:30-11:00' => ['10:30:00', '12:00:00'],
            '11:00-12:30' => ['12:00:00', '13:30:00'],
            '12:30-14:00' => ['13:30:00', '15:00:00'],
            '14:00-15:30' => ['15:00:00', '16:30:00'],
            '15:30-17:00' => ['16:30:00', '18:00:00'],
            '17:00-18:30' => ['18:00:00', '19:30:00'],
            '18:30-20:00' => ['19:30:00', '21:00:00'],
            '20:00-21:30' => ['21:00:00', '22:00:00'],
        ];

        DB::table(
            'professor_availabilities'
        )
            ->orderBy('id')
            ->chunkById(
                200,
                function ($rows) use ($map) {
                    foreach ($rows as $row) {
                        $start = substr(
                            (string) $row->start_time,
                            0,
                            5
                        );

                        $end = substr(
                            (string) $row->end_time,
                            0,
                            5
                        );

                        $key =
                            $start
                            . '-'
                            . $end;

                        if (!isset($map[$key])) {
                            continue;
                        }

                        [
                            $oldStart,
                            $oldEnd,
                        ] = $map[$key];

                        DB::table(
                            'professor_availabilities'
                        )
                            ->where(
                                'id',
                                $row->id
                            )
                            ->update([
                                'start_time' =>
                                    $oldStart,
                                'end_time' =>
                                    $oldEnd,
                                'updated_at' =>
                                    now(),
                            ]);
                    }
                },
                'id'
            );
    }
};