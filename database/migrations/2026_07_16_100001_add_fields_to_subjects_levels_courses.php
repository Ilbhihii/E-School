<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subjects : ajouter description et image
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('subjects', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
        });

        // Levels : ajouter order
        Schema::table('levels', function (Blueprint $table) {
            if (!Schema::hasColumn('levels', 'order')) {
                $table->integer('order')->default(0)->after('description');
            }
        });

        // Courses : ajouter module_id (nullable)
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'module_id')) {
                $table->foreignId('module_id')
                    ->nullable()
                    ->after('level_id')
                    ->constrained()
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('subjects', 'description')) {
                $table->dropColumn('description');
            }
        });

        Schema::table('levels', function (Blueprint $table) {
            if (Schema::hasColumn('levels', 'order')) {
                $table->dropColumn('order');
            }

        });

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'module_id')) {
                $table->dropForeign(['module_id']);
                $table->dropColumn('module_id');
            }
        });
    }
};
