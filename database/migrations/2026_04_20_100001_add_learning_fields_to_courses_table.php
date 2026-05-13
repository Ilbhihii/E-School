<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'level_id')) {
                $table->foreignId('level_id')
                      ->nullable()
                      ->constrained('levels')
                      ->cascadeOnDelete()
                      ->after('id');
            }
            if (!Schema::hasColumn('courses', 'video_url')) {
                $table->text('video_url')->nullable()->after('video');
            }
            if (!Schema::hasColumn('courses', 'order')) {
                $table->integer('order')->default(1)->after('video_url');
            }
            if (!Schema::hasColumn('courses', 'is_free')) {
                $table->boolean('is_free')->default(true)->after('order');
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'level_id')) {
                $table->dropForeign(['level_id']);
                $table->dropColumn('level_id');
            }
            if (Schema::hasColumn('courses', 'video_url')) {
                $table->dropColumn('video_url');
            }
            if (Schema::hasColumn('courses', 'order')) {
                $table->dropColumn('order');
            }
        });
    }
};

