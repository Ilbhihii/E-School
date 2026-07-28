<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vocal_test_submissions', function (Blueprint $table) {
            $table->enum('test_mode', [
                'reading',
                'tajwid',
                'hifd',
            ])->nullable()->after('reading_text');

            // Set test_mode from prompt's test_mode for existing submissions
            // (via a subquery - handled in fallback logic below)

            $table->unsignedTinyInteger('score_pronunciation')->nullable()->after('score');
            $table->unsignedTinyInteger('score_tajwid')->nullable()->after('score_pronunciation');
            $table->unsignedTinyInteger('score_memorization')->nullable()->after('score_tajwid');
            $table->unsignedTinyInteger('score_fluency')->nullable()->after('score_memorization');
            $table->unsignedTinyInteger('final_score')->nullable()->after('score_fluency');
        });
    }

    public function down(): void
    {
        Schema::table('vocal_test_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'test_mode',
                'score_pronunciation',
                'score_tajwid',
                'score_memorization',
                'score_fluency',
                'final_score',
            ]);
        });
    }
};
