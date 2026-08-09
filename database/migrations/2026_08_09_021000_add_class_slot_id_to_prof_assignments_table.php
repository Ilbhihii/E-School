<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('prof_assignments')) {
            return;
        }

        if (!Schema::hasColumn(
            'prof_assignments',
            'class_slot_id'
        )) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) {
                    $table
                        ->foreignId('class_slot_id')
                        ->nullable()
                        ->after('subject_id')
                        ->constrained('class_slots')
                        ->nullOnDelete();
                }
            );
        }

        /*
         * IMPORTANT MySQL :
         * prof_assignment_unique peut être utilisé par la clé étrangère
         * prof_id. Si on le supprime avant de créer un autre index qui
         * commence par prof_id, MySQL retourne l'erreur 1553 :
         * "Cannot drop index ... needed in a foreign key constraint".
         *
         * On crée donc D'ABORD le nouvel index unique, puis on supprime
         * l'ancien. Le nouvel index commence par prof_id et continue ainsi
         * à supporter correctement la clé étrangère.
         */
        if (!$this->indexExists(
            'prof_assignment_slot_unique'
        )) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) {
                    $table->unique(
                        [
                            'prof_id',
                            'subject_id',
                            'level_id',
                            'class_id',
                            'class_slot_id',
                        ],
                        'prof_assignment_slot_unique'
                    );
                }
            );
        }

        /*
         * L'ancienne contrainte empêchait un professeur d'avoir
         * D1 ET D2 sur le même parcours.
         */
        $this->dropOldUniqueIfExists();
    }

    public function down(): void
    {
        if (!Schema::hasTable('prof_assignments')) {
            return;
        }

        if ($this->indexExists(
            'prof_assignment_slot_unique'
        )) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) {
                    $table->dropUnique(
                        'prof_assignment_slot_unique'
                    );
                }
            );
        }

        if (Schema::hasColumn(
            'prof_assignments',
            'class_slot_id'
        )) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) {
                    $table->dropForeign([
                        'class_slot_id',
                    ]);

                    $table->dropColumn(
                        'class_slot_id'
                    );
                }
            );
        }

        if (!$this->indexExists(
            'prof_assignment_unique'
        )) {
            Schema::table(
                'prof_assignments',
                function (Blueprint $table) {
                    $table->unique(
                        [
                            'prof_id',
                            'level_id',
                            'class_id',
                            'subject_id',
                        ],
                        'prof_assignment_unique'
                    );
                }
            );
        }
    }

    private function dropOldUniqueIfExists(): void
    {
        if (!$this->indexExists(
            'prof_assignment_unique'
        )) {
            return;
        }

        Schema::table(
            'prof_assignments',
            function (Blueprint $table) {
                $table->dropUnique(
                    'prof_assignment_unique'
                );
            }
        );
    }

    private function indexExists(
        string $indexName
    ): bool {
        $driver = DB::connection()
            ->getDriverName();

        if ($driver === 'mysql') {
            $rows = DB::select(
                'SHOW INDEX FROM prof_assignments '
                . 'WHERE Key_name = ?',
                [$indexName]
            );

            return !empty($rows);
        }

        /*
         * Le projet de production utilise MySQL.
         * Pour les autres moteurs, Laravel tentera simplement
         * de gérer les index via le schéma.
         */
        return false;
    }
};
