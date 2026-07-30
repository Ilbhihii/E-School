<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('live_access_logs')) {
            return;
        }

        Schema::create(
            'live_access_logs',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('live_id')
                    ->constrained('lives')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->string('status', 20);
                $table->string('reason', 80)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(
                    ['live_id', 'status', 'created_at'],
                    'live_access_logs_live_status_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('live_access_logs');
    }
};