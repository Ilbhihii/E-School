<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            // Drop subject_id FK and column if it exists
            if (Schema::hasColumn('levels', 'subject_id')) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            }

            // Add class_id (nullable, FK to class_rooms)
            if (!Schema::hasColumn('levels', 'class_id')) {
                $table->foreignId('class_id')
                      ->nullable()
                      ->constrained('class_rooms')
                      ->cascadeOnDelete()
                      ->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            // Drop class_id
            if (Schema::hasColumn('levels', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }

            // Restore subject_id
            if (!Schema::hasColumn('levels', 'subject_id')) {
                $table->foreignId('subject_id')
                      ->nullable()
                      ->constrained()
                      ->cascadeOnDelete()
                      ->after('id');
            }
        });
    }
};
