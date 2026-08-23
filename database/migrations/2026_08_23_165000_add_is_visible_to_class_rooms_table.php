<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('class_rooms')
            && !Schema::hasColumn('class_rooms', 'is_visible')
        ) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table
                    ->boolean('is_visible')
                    ->default(true)
                    ->after('admission_mode');
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('class_rooms')
            && Schema::hasColumn('class_rooms', 'is_visible')
        ) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table->dropColumn('is_visible');
            });
        }
    }
};
