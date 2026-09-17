<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlexibleTestsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('flexible_tests')) {
            Schema::create('flexible_tests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->unsignedBigInteger('level_id')->nullable();
                $table->unsignedBigInteger('class_id')->nullable();

                $table->string('title');
                $table->text('instructions')->nullable();

                $table->string('source_type', 30)->default('text');
                $table->longText('source_text')->nullable();
                $table->json('source_files')->nullable();

                $table->string('response_type', 30)->default('written');
                $table->string('written_response_mode', 30)->nullable();
                $table->string('vocal_mode', 30)->nullable();
                $table->json('qcm_questions')->nullable();

                $table->unsignedInteger('preparation_seconds')->default(0);
                $table->unsignedInteger('maximum_duration')->default(120);
                $table->boolean('is_active')->default(true);

                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['subject_id', 'level_id', 'class_id']);
                $table->index('response_type');
                $table->index('is_active');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('flexible_tests');
    }
}