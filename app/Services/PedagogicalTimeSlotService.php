<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PedagogicalTimeSlotService
{
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
            $normalized =
                Carbon::parse(
                    $time
                )->format('H:i');
        } catch (\Throwable $exception) {
            return null;
        }

        if (
            $normalized < '08:00'
            || $normalized > '22:00'
        ) {
            return null;
        }

        return $normalized;
    }

    /**
     * Numéro = rang chronologique de l'heure dans la journée.
     *
     * Si une nouvelle heure est créée, les numéros du jour
     * sont recalculés puis tous les codes stockés dépendants
     * de ce jour sont reconstruits.
     */
    public function slotNumber(
        int $day,
        ?string $time,
        bool $create = true
    ): ?int {
        if (
            $day < 1
            || $day > 7
        ) {
            return null;
        }

        $time =
            $this->normalizeTime(
                $time
            );

        if (!$time) {
            return null;
        }

        if (
            !Schema::hasTable(
                'pedagogical_time_slots'
            )
        ) {
            return null;
        }

        $existing =
            DB::table(
                'pedagogical_time_slots'
            )
                ->where(
                    'day_of_week',
                    $day
                )
                ->whereTime(
                    'start_time',
                    '=',
                    $time
                )
                ->value(
                    'slot_number'
                );

        if ($existing) {
            return
                (int) $existing;
        }

        if (!$create) {
            return
                $this->previewSlotNumber(
                    $day,
                    $time
                );
        }

        $created = false;

        $number =
            DB::transaction(
                function () use (
                    $day,
                    $time,
                    &$created
                ) {
                    $rows =
                        DB::table(
                            'pedagogical_time_slots'
                        )
                            ->where(
                                'day_of_week',
                                $day
                            )
                            ->orderBy(
                                'start_time'
                            )
                            ->lockForUpdate()
                            ->get();

                    foreach ($rows as $row) {
                        if (
                            substr(
                                (string) $row->start_time,
                                0,
                                5
                            ) === $time
                        ) {
                            return
                                (int)
                                    $row->slot_number;
                        }
                    }

                    $times =
                        $rows
                            ->map(
                                fn ($row) =>
                                    substr(
                                        (string) $row->start_time,
                                        0,
                                        5
                                    )
                            )
                            ->push('08:00')
                            ->push($time)
                            ->unique()
                            ->sort()
                            ->values();

                    /*
                     * Libérer temporairement les numéros uniques.
                     */
                    DB::table(
                        'pedagogical_time_slots'
                    )
                        ->where(
                            'day_of_week',
                            $day
                        )
                        ->update([
                            'slot_number' =>
                                DB::raw(
                                    'slot_number + 1000'
                                ),
                        ]);

                    foreach (
                        $times
                        as $index => $knownTime
                    ) {
                        DB::table(
                            'pedagogical_time_slots'
                        )->updateOrInsert(
                            [
                                'day_of_week' =>
                                    $day,
                                'start_time' =>
                                    $knownTime
                                    . ':00',
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

                    $created = true;

                    $position =
                        $times
                            ->search(
                                $time,
                                true
                            );

                    return
                        $position === false
                            ? null
                            : $position + 1;
                }
            );

        /*
         * IMPORTANT :
         * Après insertion d'une nouvelle heure, 09:00 peut passer
         * par exemple de 3 à 4. On synchronise donc les codes
         * déjà stockés dans class_user/courses/assignments/messages.
         */
        if (
            $created
            && class_exists(
                PedagogicalScopeCodeRebuildService::class
            )
        ) {
            app(
                PedagogicalScopeCodeRebuildService::class
            )->rebuildDay(
                $day
            );
        }

        return $number;
    }

    public function previewSlotNumber(
        int $day,
        ?string $time
    ): ?int {
        if (
            $day < 1
            || $day > 7
        ) {
            return null;
        }

        $time =
            $this->normalizeTime(
                $time
            );

        if (!$time) {
            return null;
        }

        if (
            !Schema::hasTable(
                'pedagogical_time_slots'
            )
        ) {
            return null;
        }

        $times =
            DB::table(
                'pedagogical_time_slots'
            )
                ->where(
                    'day_of_week',
                    $day
                )
                ->pluck(
                    'start_time'
                )
                ->map(
                    fn ($value) =>
                        substr(
                            (string) $value,
                            0,
                            5
                        )
                )
                ->push('08:00')
                ->push($time)
                ->unique()
                ->sort()
                ->values();

        $position =
            $times
                ->search(
                    $time,
                    true
                );

        return
            $position === false
                ? null
                : $position + 1;
    }

    public function reindexDay(
        int $day,
        bool $rebuildCodes = true
    ): void {
        if (
            $day < 1
            || $day > 7
            || !Schema::hasTable(
                'pedagogical_time_slots'
            )
        ) {
            return;
        }

        DB::transaction(
            function () use ($day) {
                $times =
                    DB::table(
                        'pedagogical_time_slots'
                    )
                        ->where(
                            'day_of_week',
                            $day
                        )
                        ->orderBy(
                            'start_time'
                        )
                        ->lockForUpdate()
                        ->pluck(
                            'start_time'
                        )
                        ->map(
                            fn ($value) =>
                                substr(
                                    (string) $value,
                                    0,
                                    5
                                )
                        )
                        ->push('08:00')
                        ->unique()
                        ->sort()
                        ->values();

                DB::table(
                    'pedagogical_time_slots'
                )
                    ->where(
                        'day_of_week',
                        $day
                    )
                    ->update([
                        'slot_number' =>
                            DB::raw(
                                'slot_number + 1000'
                            ),
                    ]);

                foreach (
                    $times
                    as $index => $time
                ) {
                    DB::table(
                        'pedagogical_time_slots'
                    )->updateOrInsert(
                        [
                            'day_of_week' =>
                                $day,
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
        );

        if (
            $rebuildCodes
            && class_exists(
                PedagogicalScopeCodeRebuildService::class
            )
        ) {
            app(
                PedagogicalScopeCodeRebuildService::class
            )->rebuildDay(
                $day
            );
        }
    }

    public function reindexAll(
        bool $rebuildCodes = true
    ): void {
        for (
            $day = 1;
            $day <= 7;
            $day++
        ) {
            $this->reindexDay(
                $day,
                $rebuildCodes
            );
        }
    }

    public function map(): array
    {
        if (
            !Schema::hasTable(
                'pedagogical_time_slots'
            )
        ) {
            return [];
        }

        $map = [];

        $rows =
            DB::table(
                'pedagogical_time_slots'
            )
                ->orderBy(
                    'day_of_week'
                )
                ->orderBy(
                    'start_time'
                )
                ->get([
                    'day_of_week',
                    'start_time',
                ]);

        foreach (
            $rows
                ->groupBy(
                    'day_of_week'
                )
            as $day => $dayRows
        ) {
            $times =
                $dayRows
                    ->map(
                        fn ($row) =>
                            substr(
                                (string) $row->start_time,
                                0,
                                5
                            )
                    )
                    ->push('08:00')
                    ->unique()
                    ->sort()
                    ->values();

            foreach (
                $times
                as $index => $time
            ) {
                $map[
                    (string) $day
                ][
                    $time
                ] =
                    $index + 1;
            }
        }

        return $map;
    }
}