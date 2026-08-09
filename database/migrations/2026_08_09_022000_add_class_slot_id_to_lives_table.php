<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lives', function (Blueprint $table) {
            if (!Schema::hasColumn('lives', 'class_slot_id')) {
                $table
                    ->foreignId('class_slot_id')
                    ->nullable()
                    ->after('class_id')
                    ->constrained('class_slots')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('lives', function (Blueprint $table) {
            if (Schema::hasColumn('lives', 'class_slot_id')) {
                $table->dropForeign(['class_slot_id']);
                $table->dropColumn('class_slot_id');
            }
        });
    }
};
