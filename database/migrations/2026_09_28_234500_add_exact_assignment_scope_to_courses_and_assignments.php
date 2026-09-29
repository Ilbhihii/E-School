<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['courses', 'assignments'] as $tableName) {
            if (!Schema::hasTable($tableName)) continue;

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'assignment_code')) {
                    $table->string('assignment_code', 64)->nullable()->index();
                }

                if (!Schema::hasColumn($tableName, 'assignment_day_of_week')) {
                    $table->unsignedTinyInteger('assignment_day_of_week')->nullable()->index();
                }

                if (!Schema::hasColumn($tableName, 'assignment_start_time')) {
                    $table->time('assignment_start_time')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['courses', 'assignments'] as $tableName) {
            if (!Schema::hasTable($tableName)) continue;

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                foreach ([
                    'assignment_code',
                    'assignment_day_of_week',
                    'assignment_start_time',
                ] as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};