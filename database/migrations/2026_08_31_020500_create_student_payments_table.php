<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();

            // Relation logique vers users.id.
            // On garde volontairement un index sans contrainte FOREIGN KEY
            // pour rester compatible avec la base MySQL existante du projet.
            $table->unsignedBigInteger('user_id')->index();

            $table->string('plan_type', 30);
            $table->decimal('amount', 10, 2)->nullable();

            $table->date('paid_at');
            $table->date('starts_at');
            $table->date('expires_at');

            $table->string('payment_method', 40)->nullable();
            $table->string('status', 30)->default('paid');
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['plan_type', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_payments');
    }
}
