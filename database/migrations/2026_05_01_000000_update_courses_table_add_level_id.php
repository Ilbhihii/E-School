<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop class_id if it exists
            if (Schema::hasColumn('courses', 'class_id')) {
                // Check if foreign key exists before dropping
                $foreignKeys = \DB::select(
                    "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'courses' AND COLUMN_NAME = 'class_id' AND TABLE_SCHEMA = DATABASE()"
                );
                if (!empty($foreignKeys)) {
                    $table->dropForeign(['class_id']);
                }
                $table->dropColumn('class_id');
            }

            // Add level_id if it doesn't exist
            if (!Schema::hasColumn('courses', 'level_id')) {
                $table->foreignId('level_id')
                      ->after('subject_id')
                      ->nullable()
                      ->constrained()
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop level_id if it exists
            if (Schema::hasColumn('courses', 'level_id')) {
                $foreignKeys = \DB::select(
                    "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'courses' AND COLUMN_NAME = 'level_id' AND TABLE_SCHEMA = DATABASE()"
                );
                if (!empty($foreignKeys)) {
                    $table->dropForeign(['level_id']);
                }
                $table->dropColumn('level_id');
            }

            // Restore class_id
            if (!Schema::hasColumn('courses', 'class_id')) {
                $table->foreignId('class_id')
                      ->nullable()
                      ->after('subject_id')
                      ->constrained('class_rooms')
                      ->cascadeOnDelete();
            }
        });
    }
};

