<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lives')) {
            return;
        }

        Schema::table('lives', function (Blueprint $table) {
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
                    ->time(
                        'assignment_start_time'
                    )
                    ->nullable();
            }

            if (
                !Schema::hasColumn(
                    'lives',
                    'assignment_end_time'
                )
            ) {
                $table
                    ->time(
                        'assignment_end_time'
                    )
                    ->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('lives')) {
            return;
        }

        Schema::table('lives', function (Blueprint $table) {
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
        });
    }
};