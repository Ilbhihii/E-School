<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('absences')
            && !Schema::hasColumn(
                'absences',
                'class_slot_id'
            )
        ) {
            Schema::table(
                'absences',
                function (Blueprint $table) {
                    $table
                        ->foreignId('class_slot_id')
                        ->nullable()
                        ->after('class_id')
                        ->constrained('class_slots')
                        ->nullOnDelete();

                    $table->index(
                        [
                            'subject_id',
                            'level_id',
                            'class_id',
                            'class_slot_id',
                        ],
                        'absences_path_slot_idx'
                    );
                }
            );
        }

        $this->backfillUniqueSlots();
    }

    public function down(): void
    {
        if (
            Schema::hasTable('absences')
            && Schema::hasColumn(
                'absences',
                'class_slot_id'
            )
        ) {
            Schema::table(
                'absences',
                function (Blueprint $table) {
                    $table->dropIndex(
                        'absences_path_slot_idx'
                    );

                    $table->dropConstrainedForeignId(
                        'class_slot_id'
                    );
                }
            );
        }
    }

    /**
     * On ne devine jamais arbitrairement D1/D2/etc.
     *
     * Une ancienne absence reçoit class_slot_id seulement lorsque
     * l'étudiant possède exactement UN créneau compatible avec
     * Matière → Niveau → Classe.
     */
    private function backfillUniqueSlots(): void
    {
        if (
            !Schema::hasTable('class_user')
            || !Schema::hasTable('class_slots')
            || !Schema::hasColumn(
                'class_user',
                'class_slot_id'
            )
        ) {
            return;
        }

        DB::table('absences')
            ->whereNull('class_slot_id')
            ->whereNotNull('subject_id')
            ->whereNotNull('level_id')
            ->whereNotNull('class_id')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($absences) {
                    foreach ($absences as $absence) {
                        $slotIds = DB::table(
                            'class_user as cu'
                        )
                            ->join(
                                'class_slots as cs',
                                'cs.id',
                                '=',
                                'cu.class_slot_id'
                            )
                            ->where(
                                'cu.user_id',
                                $absence->user_id
                            )
                            ->where(
                                'cu.subject_id',
                                $absence->subject_id
                            )
                            ->where(
                                'cu.class_id',
                                $absence->class_id
                            )
                            ->where(
                                'cs.subject_id',
                                $absence->subject_id
                            )
                            ->where(
                                'cs.level_id',
                                $absence->level_id
                            )
                            ->where(
                                'cs.class_id',
                                $absence->class_id
                            )
                            ->where(
                                'cs.is_active',
                                true
                            )
                            ->whereNotNull(
                                'cu.class_slot_id'
                            )
                            ->pluck(
                                'cu.class_slot_id'
                            )
                            ->unique()
                            ->values();

                        if ($slotIds->count() !== 1) {
                            continue;
                        }

                        DB::table('absences')
                            ->where(
                                'id',
                                $absence->id
                            )
                            ->update([
                                'class_slot_id' =>
                                    (int) $slotIds->first(),
                                'updated_at' => now(),
                            ]);
                    }
                }
            );
    }
};
