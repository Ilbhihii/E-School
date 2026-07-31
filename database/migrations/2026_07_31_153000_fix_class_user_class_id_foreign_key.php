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
        ) {
            return;
        }

        /*
         * Nettoyer uniquement les anciennes assignations
         * dont l'utilisateur ou la matière n'existe plus.
         *
         * Aucune assignation valide n'est supprimée.
         */
        $orphanAssignmentIds = DB::table(
                'class_user as cu'
            )
            ->leftJoin(
                'users as u',
                'cu.user_id',
                '=',
                'u.id'
            )
            ->leftJoin(
                'subjects as s',
                'cu.subject_id',
                '=',
                's.id'
            )
            ->where(
                function ($query) {
                    $query
                        ->whereNull('u.id')
                        ->orWhereNull('s.id');
                }
            )
            ->pluck('cu.id');

        if ($orphanAssignmentIds->isNotEmpty()) {
            DB::table('class_user')
                ->whereIn(
                    'id',
                    $orphanAssignmentIds
                )
                ->delete();
        }

        /*
         * Après le nettoyage des lignes réellement
         * orphelines, vérifier qu'il ne reste aucun
         * class_id impossible à relier à class_rooms.
         *
         * Une ligne liée à un utilisateur valide n'est
         * jamais supprimée automatiquement.
         */
        $invalidRows = DB::table(
                'class_user as cu'
            )
            ->leftJoin(
                'class_rooms as cr',
                'cu.class_id',
                '=',
                'cr.id'
            )
            ->leftJoin(
                'users as u',
                'cu.user_id',
                '=',
                'u.id'
            )
            ->leftJoin(
                'subjects as s',
                'cu.subject_id',
                '=',
                's.id'
            )
            ->whereNull('cr.id')
            ->select([
                'cu.id',
                'cu.user_id',
                'cu.class_id',
                'cu.subject_id',
                'u.name as user_name',
                's.name as subject_name',
            ])
            ->limit(20)
            ->get();

        if ($invalidRows->isNotEmpty()) {
            $details = $invalidRows
                ->map(
                    function ($row) {
                        return sprintf(
                            '#%s user=%s (%s) class=%s '
                            . 'subject=%s (%s)',
                            $row->id,
                            $row->user_id,
                            $row->user_name
                                ?? 'utilisateur absent',
                            $row->class_id,
                            $row->subject_id
                                ?? 'NULL',
                            $row->subject_name
                                ?? 'matière absente'
                        );
                    }
                )
                ->implode('; ');

            throw new \RuntimeException(
                'La clé étrangère ne peut pas encore '
                . 'être remplacée. Certaines assignations '
                . 'appartiennent à des utilisateurs valides, '
                . 'mais leur class_id ne correspond à aucune '
                . 'ligne de class_rooms. Corrigez ces lignes '
                . 'manuellement. Détails : '
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
        if (
            !Schema::hasTable('class_user')
            || !Schema::hasTable('classes')
        ) {
            return;
        }

        $invalidRows = DB::table(
                'class_user as cu'
            )
            ->leftJoin(
                'classes as c',
                'cu.class_id',
                '=',
                'c.id'
            )
            ->whereNull('c.id')
            ->exists();

        if ($invalidRows) {
            throw new \RuntimeException(
                'Rollback impossible : des class_id '
                . 'de class_user n’existent pas dans '
                . 'l’ancienne table classes.'
            );
        }

        $foreignKeys = DB::select(
            "
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'class_user'
                  AND COLUMN_NAME = 'class_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "
        );

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

        DB::statement(
            "
                ALTER TABLE `class_user`
                ADD CONSTRAINT
                    `class_user_class_id_foreign`
                FOREIGN KEY (`class_id`)
                REFERENCES `classes` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            "
        );
    }
};