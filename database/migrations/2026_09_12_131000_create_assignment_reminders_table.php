<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentRemindersTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('assignment_reminders')) {
            return;
        }

        Schema::create('assignment_reminders', function (Blueprint $table) {
            $table->id();

            // Pas de FK volontairement : compatibilité avec les anciennes bases.
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('recipient_user_id')->nullable();

            $table->string('kind', 50);
            $table->string('channel', 20)->default('email');
            $table->string('recipient', 190)->nullable();
            $table->unsignedInteger('missing_count')->nullable();
            $table->string('status', 30)->default('sent');
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['assignment_id', 'student_id', 'kind'], 'assignment_reminders_assignment_student_idx');
            $table->index(['student_id', 'kind', 'sent_at'], 'assignment_reminders_student_kind_idx');
            $table->index(['recipient_user_id', 'kind', 'sent_at'], 'assignment_reminders_recipient_kind_idx');
            $table->index(['status', 'sent_at'], 'assignment_reminders_status_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignment_reminders');
    }
}
