<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('new_courses')->default(true);
            $table->boolean('live_reminders')->default(true);
            $table->boolean('appointment_updates')->default(true);
            $table->boolean('progress_updates')->default(true);
            $table->boolean('vocal_test_feedback')->default(true);
            $table->boolean('promotional')->default(false);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
