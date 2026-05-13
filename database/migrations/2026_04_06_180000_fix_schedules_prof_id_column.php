<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('schedules')) {
            if (Schema::hasColumn('schedules', 'teacher_id') && !Schema::hasColumn('schedules', 'prof_id')) {
                Schema::table('schedules', function (Blueprint $table) {
                    $table->renameColumn('teacher_id', 'prof_id');
                });
            } elseif (!Schema::hasColumn('schedules', 'prof_id')) {
                Schema::table('schedules', function (Blueprint $table) {
                    $table->unsignedBigInteger('prof_id')->after('id');
                });
            }

            // Ensure foreign key and index
            Schema::table('schedules', function (Blueprint $table) {
                $table->foreign('prof_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('prof_id');
            });
        }
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['prof_id']);
            $table->dropIndex(['prof_id']);
        });
        
        if (Schema::hasColumn('schedules', 'prof_id') && !Schema::hasColumn('schedules', 'teacher_id')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->renameColumn('prof_id', 'teacher_id');
            });
        }
    }
};
?>

