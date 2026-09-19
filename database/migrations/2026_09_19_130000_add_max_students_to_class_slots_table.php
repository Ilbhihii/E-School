<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasColumn(
                'class_slots',
                'max_students'
            )
        ) {
            Schema::table(
                'class_slots',
                function (Blueprint $table) {
                    $table
                        ->unsignedTinyInteger(
                            'max_students'
                        )
                        ->default(12)
                        ->after('is_active');
                }
            );
        }
    }

    public function down(): void
    {
        if (
            Schema::hasColumn(
                'class_slots',
                'max_students'
            )
        ) {
            Schema::table(
                'class_slots',
                function (Blueprint $table) {
                    $table->dropColumn(
                        'max_students'
                    );
                }
            );
        }
    }
};