<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoiceSubmissionToAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'voice_path')) {
                $table->string('voice_path')->nullable()->after('file');
            }

            if (!Schema::hasColumn('assignments', 'voice_mime_type')) {
                $table->string('voice_mime_type', 120)->nullable()->after('voice_path');
            }

            if (!Schema::hasColumn('assignments', 'voice_duration_seconds')) {
                $table->unsignedInteger('voice_duration_seconds')->nullable()->after('voice_mime_type');
            }
        });
    }

    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'voice_path',
                'voice_mime_type',
                'voice_duration_seconds',
            ] as $column) {
                if (Schema::hasColumn('assignments', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
}
