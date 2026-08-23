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
            && !Schema::hasColumn('class_rooms', 'admission_mode')
        ) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table
                    ->string('admission_mode', 20)
                    ->default('contact')
                    ->after('level_id');
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('class_rooms')
            && Schema::hasColumn('class_rooms', 'admission_mode')
        ) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table->dropColumn('admission_mode');
            });
        }
    }
};
