<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('class_user')
            || !Schema::hasTable('class_rooms')
            || !Schema::hasTable('classes')
        ) {
            return;
        }

        /*
         * Mapping validé sur la base de production :
         *
         * ancienne classes.id = 39, "Avancée"
         *     -> class_rooms.id = 202, "Avancé"
         *
         * ancienne classes.id = 40, "intermédiaire"
         *     -> class_rooms.id = 201, "Intermédiaire"
         *
         * Ces deux nouvelles classes appartiennent au niveau :
         * Coran -> Apprentissage & Tajwid.
         *
         * Aucune classe et aucune assignation ne sont supprimées.
         */
        $mappings = [
            39 => 202,
            40 => 201,
        ];

        /*
         * Vérifier les classes cibles avant toute modification.
         */
        $targets = DB::table('class_rooms as cr')
            ->join(
                'levels as l',
                'cr.level_id',
                '=',
                'l.id'
            )
            ->whereIn(
                'cr.id',
                array_values($mappings)
            )
            ->select([
                'cr.id',
                'cr.name',
                'l.id as level_id',
                'l.name as level_name',
                'l.subject_id',
            ])
            ->get()
            ->keyBy('id');

        foreach ($mappings as $oldId => $newId) {
            $target = $targets->get($newId);

            if (!$target) {
                throw new \RuntimeException(
                    'La classe cible class_rooms.id='
                    . $newId
                    . ' est introuvable.'
                );
            }

            if (
                (int) $target->subject_id !== 10
                || trim((string) $target->level_name)
                    !== 'Apprentissage & Tajwid'
            ) {
                throw new \RuntimeException(
                    'La classe cible '
                    . $newId
                    . ' n’appartient pas au parcours '
                    . 'Coran → Apprentissage & Tajwid.'
                );
            }
        }

        /*
         * Vérifier qu'il ne reste pas d'autres class_id
         * invalides en dehors des deux anciens IDs connus.
         */
        $unexpectedInvalidRows = DB::table(
                'class_user as cu'
            )
            ->leftJoin(
                'class_rooms as cr',
                'cu.class_id',
                '=',
                'cr.id'
            )
            ->whereNull('cr.id')
            ->whereNotIn(
                'cu.class_id',
                array_keys($mappings)
            )
            ->select([
                'cu.id',
                'cu.user_id',
                'cu.class_id',
                'cu.subject_id',
            ])
            ->get();

        if ($unexpectedInvalidRows->isNotEmpty()) {
            $details = $unexpectedInvalidRows
                ->map(
                    function ($row) {
                        return sprintf(
                            '#%s user=%s class=%s subject=%s',
                            $row->id,
                            $row->user_id,
                            $row->class_id,
                            $row->subject_id ?? 'NULL'
                        );
                    }
                )
                ->implode('; ');

            throw new \RuntimeException(
                'D’autres class_id invalides ont été détectés. '
                . 'Aucune donnée n’a été supprimée. Détails : '
                . $details
            );
        }

        $foreignKeys = DB::select(
            "
                SELECT
                    CONSTRAINT_NAME,
                    REFERENCED_TABLE_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'class_user'
                  AND COLUMN_NAME = 'class_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "
        );

        $alreadyCorrect = collect($foreignKeys)
            ->contains(
                function ($foreignKey) {
                    return
                        $foreignKey
                            ->REFERENCED_TABLE_NAME
                        === 'class_rooms';
                }
            );

        if ($alreadyCorrect) {
            return;
        }

        /*
         * Supprimer uniquement l'ancienne contrainte.
         * Les tables et les lignes restent présentes.
         */
        foreach ($foreignKeys as $foreignKey) {
            $constraintName = str_replace(
                '`',
                '``',
                $foreignKey->CONSTRAINT_NAME
            );

            DB::statement(
                "ALTER TABLE `class_user` "
                . "DROP FOREIGN KEY `"
                . $constraintName
                . "`"
            );
        }

        /*
         * Convertir uniquement les anciennes assignations
         * Coran 39/40 vers les classes officielles 202/201.
         */
        foreach ($mappings as $oldId => $newId) {
            DB::table('class_user')
                ->where('class_id', $oldId)
                ->where('subject_id', 10)
                ->update([
                    'class_id' => $newId,
                    'updated_at' => now(),
                ]);
        }

        /*
         * Vérification finale.
         */
        $remainingInvalidRows = DB::table(
                'class_user as cu'
            )
            ->leftJoin(
                'class_rooms as cr',
                'cu.class_id',
                '=',
                'cr.id'
            )
            ->whereNull('cr.id')
            ->select([
                'cu.id',
                'cu.user_id',
                'cu.class_id',
                'cu.subject_id',
            ])
            ->get();

        if ($remainingInvalidRows->isNotEmpty()) {
            $details = $remainingInvalidRows
                ->map(
                    function ($row) {
                        return sprintf(
                            '#%s user=%s class=%s subject=%s',
                            $row->id,
                            $row->user_id,
                            $row->class_id,
                            $row->subject_id ?? 'NULL'
                        );
                    }
                )
                ->implode('; ');

            throw new \RuntimeException(
                'Des class_id invalides restent après '
                . 'la conversion. Détails : '
                . $details
            );
        }

        DB::statement(
            "
                ALTER TABLE `class_user`
                ADD CONSTRAINT
                    `class_user_class_id_foreign`
                FOREIGN KEY (`class_id`)
                REFERENCES `class_rooms` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            "
        );
    }

    public function down(): void
    {
        throw new \RuntimeException(
            'Rollback automatique désactivé : cette migration '
            . 'convertit des identifiants historiques.'
        );
    }
};