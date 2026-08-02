<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('prof_assignments')) {
            return;
        }

        if (!Schema::hasColumn(
            'prof_assignments',
            'day_of_week'
        )) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) {
                    $table
                        ->unsignedTinyInteger(
                            'day_of_week'
                        )
                        ->nullable()
                        ->after('subject_id');
                }
            );
        }

        if (!Schema::hasColumn(
            'prof_assignments',
            'start_time'
        )) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) {
                    $table
                        ->time('start_time')
                        ->nullable()
                        ->after('day_of_week');
                }
            );
        }

        if (!Schema::hasColumn(
            'prof_assignments',
            'end_time'
        )) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) {
                    $table
                        ->time('end_time')
                        ->nullable()
                        ->after('start_time');
                }
            );
        }
    }

    public function down()
    {
        if (!Schema::hasTable('prof_assignments')) {
            return;
        }

        $columns = [];

        foreach (
            [
                'day_of_week',
                'start_time',
                'end_time',
            ] as $column
        ) {
            if (
                Schema::hasColumn(
                    'prof_assignments',
                    $column
                )
            ) {
                $columns[] = $column;
            }
        }

        if (!empty($columns)) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) use (
                    $columns
                ) {
                    $table->dropColumn($columns);
                }
            );
        }
    }
};
