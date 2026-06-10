<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        // TABLE COURSES
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();

                // Admin qui a créé le cours (optionnel mais propre)
                $table->foreignId('admin_id')
                        ->constrained('users')
                        ->onDelete('cascade');

                $table->string('video')->nullable();
                $table->string('pdf')->nullable();
                $table->timestamps();
            });
        }

        // TABLE LIVES
        if (!Schema::hasTable('lives')) {
            Schema::create('lives', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('stream_url');

                $table->foreignId('admin_id')
                      ->constrained('users')
                      ->onDelete('cascade');

                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // drop the tables that were created in up()
        Schema::dropIfExists('lives');
        Schema::dropIfExists('courses');
    }
}
