<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('prof_assignments')
            || !Schema::hasTable('class_slots')
            || Schema::hasColumn(
                'prof_assignments',
                'class_slot_id'
            )
        ) {
            return;
        }

        Schema::table(
            'prof_assignments',
            function (Blueprint $table) {
                $table
                    ->foreignId('class_slot_id')
                    ->nullable()
                    ->after('subject_id')
                    ->constrained('class_slots')
                    ->nullOnDelete();

                $table->index(
                    'class_slot_id',
                    'prof_assignments_slot_idx'
                );
            }
        );
    }

    public function down(): void
    {
        /*
         * Migration de sécurité :
         * on ne supprime pas class_slot_id au rollback car la colonne
         * peut avoir été créée par un patch précédent.
         */
    }
};
