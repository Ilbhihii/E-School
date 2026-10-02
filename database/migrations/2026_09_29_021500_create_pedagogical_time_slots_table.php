<?php

use App\Services\StudentSlotOrdinalService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable(
                'pedagogical_time_slots'
            )
        ) {
            Schema::create(
                'pedagogical_time_slots',
                function (
                    Blueprint $table
                ) {
                    $table->id();

                    $table
                        ->unsignedTinyInteger(
                            'day_of_week'
                        );

                    $table
                        ->time(
                            'start_time'
                        );

                    $table
                        ->unsignedSmallInteger(
                            'slot_number'
                        );

                    $table->timestamps();

                    $table->unique(
                        [
                            'day_of_week',
                            'start_time',
                        ],
                        'pts_day_time_unique'
                    );

                    $table->index(
                        [
                            'day_of_week',
                            'slot_number',
                        ],
                        'pts_day_number_index'
                    );
                }
            );
        }

        app(
            StudentSlotOrdinalService::class
        )->bootstrap();
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'pedagogical_time_slots'
        );
    }
};