<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAdmissionModeToTestAppointments extends Migration
{
    public function up()
    {
        if (
            Schema::hasTable('test_appointments')
            && !Schema::hasColumn('test_appointments', 'admission_mode')
        ) {
            Schema::table('test_appointments', function (Blueprint $table) {
                $table->string('admission_mode', 30)
                    ->nullable()
                    ->after('class_id');

                $table->index('admission_mode');
            });
        }

        if (!Schema::hasTable('test_appointments')) {
            return;
        }

        if (
            Schema::hasColumn(
                'test_appointments',
                'vocal_test_submission_id'
            )
        ) {
            DB::table('test_appointments')
                ->whereNull('admission_mode')
                ->whereNotNull('vocal_test_submission_id')
                ->update(['admission_mode' => 'vocal_test']);
        }

        if (
            Schema::hasColumn(
                'test_appointments',
                'high_school_test_submission_id'
            )
        ) {
            DB::table('test_appointments')
                ->whereNull('admission_mode')
                ->whereNotNull('high_school_test_submission_id')
                ->update(['admission_mode' => 'test']);
        }

        if (
            Schema::hasTable('class_rooms')
            && Schema::hasColumn('class_rooms', 'admission_mode')
            && Schema::hasColumn('test_appointments', 'class_id')
        ) {
            DB::table('test_appointments')
                ->whereNull('admission_mode')
                ->whereNotNull('class_id')
                ->orderBy('id')
                ->chunkById(100, function ($appointments) {
                    $classIds = $appointments
                        ->pluck('class_id')
                        ->filter()
                        ->unique()
                        ->values();

                    $classModes = DB::table('class_rooms')
                        ->whereIn('id', $classIds)
                        ->pluck('admission_mode', 'id');

                    foreach ($appointments as $appointment) {
                        $mode = $classModes[$appointment->class_id]
                            ?? null;

                        if (in_array($mode, ['contact', 'vocal_test'], true)) {
                            DB::table('test_appointments')
                                ->where('id', $appointment->id)
                                ->update(['admission_mode' => $mode]);
                        }
                    }
                });
        }
    }

    public function down()
    {
        if (
            Schema::hasTable('test_appointments')
            && Schema::hasColumn('test_appointments', 'admission_mode')
        ) {
            Schema::table('test_appointments', function (Blueprint $table) {
                $table->dropIndex(['admission_mode']);
                $table->dropColumn('admission_mode');
            });
        }
    }
}
