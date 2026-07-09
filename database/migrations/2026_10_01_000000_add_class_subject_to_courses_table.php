<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'class_id')) {
                $table->foreignId('class_id')
                      ->nullable()
                      ->after('admin_id')
                      ->constrained('class_rooms')
                      ->onDelete('cascade');
            }
            if (!Schema::hasColumn('courses', 'subject_id')) {
                $table->foreignId('subject_id')
                      ->nullable()
                      ->after('class_id')
                      ->constrained('subjects')
                      ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['class_id', 'subject_id']);
        });
    }
};
