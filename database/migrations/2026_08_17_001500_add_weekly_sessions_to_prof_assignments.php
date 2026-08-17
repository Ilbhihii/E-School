<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('prof_assignments')) {
            return;
        }

        if (!Schema::hasColumn('prof_assignments', 'weekly_sessions')) {
            Schema::table('prof_assignments', function (Blueprint $table) {
                $table
                    ->unsignedTinyInteger('weekly_sessions')
                    ->default(1)
                    ->after('class_slot_id');
            });
        }

        if (!Schema::hasTable('prof_assignment_schedule')) {
            Schema::create('prof_assignment_schedule', function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('prof_assignment_id')
                    ->constrained('prof_assignments')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('schedule_id')
                    ->constrained('schedules')
                    ->cascadeOnDelete();

                $table->timestamps();

                $table->unique(
                    ['prof_assignment_id', 'schedule_id'],
                    'prof_assignment_schedule_unique'
                );

                $table->index(
                    ['schedule_id', 'prof_assignment_id'],
                    'prof_assignment_schedule_reverse_index'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prof_assignment_schedule');

        if (
            Schema::hasTable('prof_assignments')
            && Schema::hasColumn('prof_assignments', 'weekly_sessions')
        ) {
            Schema::table('prof_assignments', function (Blueprint $table) {
                $table->dropColumn('weekly_sessions');
            });
        }
    }
};
