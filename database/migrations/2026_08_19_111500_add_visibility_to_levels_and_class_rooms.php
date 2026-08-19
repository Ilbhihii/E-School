<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('levels')
            && !Schema::hasColumn('levels', 'is_active')
        ) {
            Schema::table('levels', function (Blueprint $table) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('order');

                $table->index(
                    ['subject_id', 'is_active'],
                    'levels_subject_active_index'
                );
            });
        }

        if (
            Schema::hasTable('class_rooms')
            && !Schema::hasColumn('class_rooms', 'is_active')
        ) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('level_id');

                $table->index(
                    ['level_id', 'is_active'],
                    'class_rooms_level_active_index'
                );
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('class_rooms')
            && Schema::hasColumn('class_rooms', 'is_active')
        ) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table->dropIndex(
                    'class_rooms_level_active_index'
                );
                $table->dropColumn('is_active');
            });
        }

        if (
            Schema::hasTable('levels')
            && Schema::hasColumn('levels', 'is_active')
        ) {
            Schema::table('levels', function (Blueprint $table) {
                $table->dropIndex(
                    'levels_subject_active_index'
                );
                $table->dropColumn('is_active');
            });
        }
    }
};
