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
        if (!Schema::hasTable('lives')) {
            return;
        }

        $needsAutoDeleteAt =
            !Schema::hasColumn(
                'lives',
                'auto_delete_at'
            );

        $needsDeletedAt =
            !Schema::hasColumn(
                'lives',
                'deleted_at'
            );

        if (
            $needsAutoDeleteAt
            || $needsDeletedAt
        ) {
            Schema::table(
                'lives',
                function (Blueprint $table) use (
                    $needsAutoDeleteAt,
                    $needsDeletedAt
                ) {
                    if ($needsAutoDeleteAt) {
                        $table->dateTime(
                            'auto_delete_at'
                        )
                            ->nullable()
                            ->index()
                            ->after('end_time');
                    }

                    if ($needsDeletedAt) {
                        $table->softDeletes();
                    }
                }
            );
        }

        /*
         * Calculer la date de suppression des anciens lives.
         *
         * Règle :
         * heure de fin + 24 heures.
         *
         * Sans heure de fin, la session est considérée
         * comme durant une heure.
         */
        DB::table('lives')
            ->whereNull('auto_delete_at')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($lives) {
                    foreach ($lives as $live) {
                        $autoDeleteAt =
                            $this->calculateAutoDeleteAt(
                                $live
                            );

                        if (!$autoDeleteAt) {
                            continue;
                        }

                        DB::table('lives')
                            ->where(
                                'id',
                                $live->id
                            )
                            ->update([
                                'auto_delete_at' =>
                                    $autoDeleteAt,
                            ]);
                    }
                }
            );
    }

    public function down(): void
    {
        if (!Schema::hasTable('lives')) {
            return;
        }

        $columns = [];

        if (
            Schema::hasColumn(
                'lives',
                'auto_delete_at'
            )
        ) {
            $columns[] = 'auto_delete_at';
        }

        if (
            Schema::hasColumn(
                'lives',
                'deleted_at'
            )
        ) {
            $columns[] = 'deleted_at';
        }

        if (empty($columns)) {
            return;
        }

        Schema::table(
            'lives',
            function (Blueprint $table) use (
                $columns
            ) {
                $table->dropColumn($columns);
            }
        );
    }

    private function calculateAutoDeleteAt(
        object $live
    ): ?Carbon {
        if (!$live->live_date) {
            return null;
        }

        $date = Carbon::parse(
            $live->live_date
        )->format('Y-m-d');

        $startTime = $this->normalizeTime(
            $live->start_time,
            '00:00:00'
        );

        $start = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $date . ' ' . $startTime,
            config('app.timezone')
        );

        if (!$live->end_time) {
            $end = $start
                ->copy()
                ->addHour();
        } else {
            $end = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $date
                    . ' '
                    . $this->normalizeTime(
                        $live->end_time,
                        $start
                            ->copy()
                            ->addHour()
                            ->format('H:i:s')
                    ),
                config('app.timezone')
            );

            if (
                $end->lessThanOrEqualTo(
                    $start
                )
            ) {
                $end->addDay();
            }
        }

        return $end->addHours(24);
    }

    private function normalizeTime(
        $time,
        string $fallback
    ): string {
        if (!$time) {
            return $fallback;
        }

        try {
            return Carbon::parse(
                $time
            )->format('H:i:s');
        } catch (\Throwable $exception) {
            return $fallback;
        }
    }
};
