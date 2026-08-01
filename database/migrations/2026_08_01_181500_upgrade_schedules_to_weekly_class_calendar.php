<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->unsignedInteger('capacity')->nullable();
                $table->string('location')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        DB::table('rooms')->updateOrInsert(
            ['name' => 'Classe 1'],
            [
                'capacity' => 20,
                'location' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if (!Schema::hasTable('schedules')) {
            Schema::create('schedules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('prof_id');
                $table->unsignedBigInteger('class_id');
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->unsignedBigInteger('level_id')->nullable();
                $table->unsignedBigInteger('room_id')->nullable();
                $table->string('subject');
                $table->timestamp('start_time');
                $table->timestamp('end_time');
                $table->date('date')->nullable();
                $table->unsignedTinyInteger('day_of_week')->nullable();
                $table->string('recurrence', 20)->default('once');
                $table->date('valid_from')->nullable();
                $table->date('valid_until')->nullable();
                $table->string('status', 20)->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['day_of_week', 'status']);
                $table->index(['room_id', 'day_of_week']);
                $table->index(['prof_id', 'day_of_week']);

                $table->foreign('prof_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('class_rooms')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->nullOnDelete();
                $table->foreign('level_id')->references('id')->on('levels')->nullOnDelete();
                $table->foreign('room_id')->references('id')->on('rooms')->nullOnDelete();
            });

            return;
        }

        $newColumns = [];

        if (!Schema::hasColumn('schedules', 'subject_id')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('class_id');
            });
            $newColumns[] = 'subject_id';
        }

        if (!Schema::hasColumn('schedules', 'level_id')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->unsignedBigInteger('level_id')->nullable()->after('subject_id');
            });
            $newColumns[] = 'level_id';
        }

        if (!Schema::hasColumn('schedules', 'room_id')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->unsignedBigInteger('room_id')->nullable()->after('level_id');
            });
            $newColumns[] = 'room_id';
        }

        if (!Schema::hasColumn('schedules', 'day_of_week')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->unsignedTinyInteger('day_of_week')->nullable()->after('date');
            });
            $newColumns[] = 'day_of_week';
        }

        if (!Schema::hasColumn('schedules', 'recurrence')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->string('recurrence', 20)->default('once')->after('day_of_week');
            });
            $newColumns[] = 'recurrence';
        }

        if (!Schema::hasColumn('schedules', 'valid_from')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->date('valid_from')->nullable()->after('recurrence');
            });
            $newColumns[] = 'valid_from';
        }

        if (!Schema::hasColumn('schedules', 'valid_until')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->date('valid_until')->nullable()->after('valid_from');
            });
            $newColumns[] = 'valid_until';
        }

        if (!Schema::hasColumn('schedules', 'status')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->string('status', 20)->default('active')->after('valid_until');
            });
            $newColumns[] = 'status';
        }

        if (!Schema::hasColumn('schedules', 'notes')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('status');
            });
            $newColumns[] = 'notes';
        }

        if (in_array('subject_id', $newColumns, true)) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->foreign('subject_id', 'schedules_subject_id_foreign')
                    ->references('id')->on('subjects')->nullOnDelete();
            });
        }

        if (in_array('level_id', $newColumns, true)) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->foreign('level_id', 'schedules_level_id_foreign')
                    ->references('id')->on('levels')->nullOnDelete();
            });
        }

        if (in_array('room_id', $newColumns, true)) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->foreign('room_id', 'schedules_room_id_foreign')
                    ->references('id')->on('rooms')->nullOnDelete();
            });
        }

        if (
            in_array('day_of_week', $newColumns, true)
            || in_array('status', $newColumns, true)
        ) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->index(['day_of_week', 'status'], 'schedules_day_status_index');
            });
        }

        if (in_array('room_id', $newColumns, true)) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->index(['room_id', 'day_of_week'], 'schedules_room_day_index');
            });
        }

        DB::table('schedules')
            ->orderBy('id')
            ->chunkById(100, function ($schedules) {
                foreach ($schedules as $schedule) {
                    $date = $schedule->date
                        ?: Carbon::parse($schedule->start_time)->toDateString();

                    $subjectId = null;
                    if (!empty($schedule->subject)) {
                        $subjectId = DB::table('subjects')
                            ->whereRaw('LOWER(name) = ?', [mb_strtolower($schedule->subject)])
                            ->value('id');
                    }

                    $levelId = DB::table('class_rooms')
                        ->where('id', $schedule->class_id)
                        ->value('level_id');

                    DB::table('schedules')
                        ->where('id', $schedule->id)
                        ->update([
                            'subject_id' => $schedule->subject_id ?? $subjectId,
                            'level_id' => $schedule->level_id ?? $levelId,
                            'day_of_week' => $schedule->day_of_week
                                ?? Carbon::parse($date)->dayOfWeekIso,
                            'recurrence' => $schedule->recurrence ?: 'once',
                            'valid_from' => $schedule->valid_from ?: $date,
                            'status' => $schedule->status ?: 'active',
                        ]);
                }
            });
    }

    public function down()
    {
        // Migration volontairement non destructive : les plannings existants sont conservés.
    }
};
