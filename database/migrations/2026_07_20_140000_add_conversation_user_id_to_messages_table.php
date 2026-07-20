<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('conversation_user_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->index(['subject_id', 'conversation_user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['subject_id', 'conversation_user_id']);
            $table->dropForeign(['conversation_user_id']);
            $table->dropColumn('conversation_user_id');
        });
    }
};
