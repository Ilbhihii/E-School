<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_user', function (Blueprint $table) {
            if (!Schema::hasColumn('class_user', 'student_slot_code')) {
                $table
                    ->string('student_slot_code', 32)
                    ->nullable()
                    ->after('schedule_id');
            }

            if (!Schema::hasColumn('class_user', 'student_day_of_week')) {
                $table
                    ->unsignedTinyInteger('student_day_of_week')
                    ->nullable()
                    ->after('student_slot_code');
            }

            if (!Schema::hasColumn('class_user', 'student_start_time')) {
                $table
                    ->time('student_start_time')
                    ->nullable()
                    ->after('student_day_of_week');
            }

            if (!Schema::hasColumn('class_user', 'student_end_time')) {
                $table
                    ->time('student_end_time')
                    ->nullable()
                    ->after('student_start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class_user', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'student_slot_code',
                'student_day_of_week',
                'student_start_time',
                'student_end_time',
            ] as $column) {
                if (Schema::hasColumn('class_user', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};