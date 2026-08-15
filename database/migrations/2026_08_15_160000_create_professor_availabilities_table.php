<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('professor_availabilities')) {
            return;
        }

        Schema::create(
            'professor_availabilities',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('prof_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->unsignedTinyInteger('day_of_week');
                $table->time('start_time');
                $table->time('end_time');
                $table->timestamps();

                $table->unique(
                    [
                        'prof_id',
                        'day_of_week',
                        'start_time',
                        'end_time',
                    ],
                    'prof_availability_unique'
                );

                $table->index(
                    [
                        'day_of_week',
                        'start_time',
                        'end_time',
                    ],
                    'prof_availability_time_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'professor_availabilities'
        );
    }
};
