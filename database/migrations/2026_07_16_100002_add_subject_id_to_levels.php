<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            if (!Schema::hasColumn('levels', 'subject_id')) {
                $table->foreignId('subject_id')
                    ->nullable()
                    ->constrained()
                    ->onDelete('cascade')
                    ->after('order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            if (Schema::hasColumn('levels', 'subject_id')) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            }
        });
    }
};
