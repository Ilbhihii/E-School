<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vocal_test_prompts', function (Blueprint $table) {
            $table->enum('test_mode', [
                'reading',
                'tajwid',
                'hifd',
            ])->default('reading')->after('reading_text');

            $table->unsignedInteger('preparation_seconds')
                ->default(0)
                ->after('test_mode');

            $table->boolean('hide_text_during_recording')
                ->default(false)
                ->after('preparation_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('vocal_test_prompts', function (Blueprint $table) {
            $table->dropColumn(['test_mode', 'preparation_seconds', 'hide_text_during_recording']);
        });
    }
};
