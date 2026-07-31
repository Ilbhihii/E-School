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
         * Supprimer uniquement les assignations réellement
         * orphelines : utilisateur ou matière supprimé.
         */
        $orphanIds = DB::table('class_user as cu')
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

        if ($orphanIds->isNotEmpty()) {
            DB::table('class_user')
                ->whereIn('id', $orphanIds)
                ->delete();
        }

        /*
         * Rechercher les assignations utilisant encore les IDs
         * de l'ancienne table classes.
         */
        $legacyRows = DB::table('class_user as cu')
            ->leftJoin(
                'class_rooms as current_class',
                'cu.class_id',
                '=',
                'current_class.id'
            )
            ->leftJoin(
                'classes as legacy_class',
                'cu.class_id',
                '=',
                'legacy_class.id'
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
            ->whereNull('current_class.id')
            ->select([
                'cu.id',
                'cu.user_id',
                'cu.class_id',
                'cu.subject_id',
                'u.name as user_name',
                's.name as subject_name',
                'legacy_class.name as legacy_class_name',
            ])
            ->get();

        $mappings = [];
        $mappingErrors = [];

        foreach ($legacyRows as $row) {
            if (!$row->legacy_class_name) {
                $mappingErrors[] = sprintf(
                    '#%s user=%s class=%s : '
                    . 'ancienne classe introuvable',
                    $row->id,
                    $row->user_id,
                    $row->class_id
                );

                continue;
            }

            $candidates = DB::table(
                    'class_rooms as cr'
                )
                ->join(
                    'levels as l',
                    'cr.level_id',
                    '=',
                    'l.id'
                )
                ->leftJoin(
                    'class_room_subject as crs',
                    'crs.class_room_id',
                    '=',
                    'cr.id'
                )
                ->whereRaw(
                    'LOWER(TRIM(cr.name)) = '
                    . 'LOWER(TRIM(?))',
                    [$row->legacy_class_name]
                )
                ->where(
                    function ($query) use ($row) {
                        $query
                            ->where(
                                'l.subject_id',
                                $row->subject_id
                            )
                            ->orWhere(
                                'crs.subject_id',
                                $row->subject_id
                            );
                    }
                )
                ->select([
                    'cr.id',
                    'cr.name',
                    'l.id as level_id',
                    'l.name as level_name',
                ])
                ->distinct()
                ->get();

            if ($candidates->count() !== 1) {
                $candidateDetails = $candidates
                    ->map(
                        function ($candidate) {
                            return sprintf(
                                '%s:%s/%s',
                                $candidate->id,
                                $candidate->level_name,
                                $candidate->name
                            );
                        }
                    )
                    ->implode(', ');

                $mappingErrors[] = sprintf(
                    '#%s user=%s (%s), ancienne classe '
                    . '%s (%s), matière %s (%s), '
                    . 'candidats=%s',
                    $row->id,
                    $row->user_id,
                    $row->user_name ?? 'inconnu',
                    $row->class_id,
                    $row->legacy_class_name,
                    $row->subject_id,
                    $row->subject_name ?? 'inconnue',
                    $candidateDetails !== ''
                        ? $candidateDetails
                        : 'aucun'
                );

                continue;
            }

            $candidate = $candidates->first();

            $mappings[] = [
                'pivot_id' => (int) $row->id,
                'old_class_id' =>
                    (int) $row->class_id,
                'new_class_id' =>
                    (int) $candidate->id,
            ];
        }

        /*
         * Tout valider avant de supprimer l'ancienne clé.
         */
        if (!empty($mappingErrors)) {
            throw new \RuntimeException(
                'Certaines anciennes classes ne peuvent '
                . 'pas être associées automatiquement à '
                . 'class_rooms. Détails : '
                . implode('; ', $mappingErrors)
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
         * Supprimer l'ancienne clé classes.id.
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
         * Convertir les anciens IDs vers les IDs class_rooms.
         */
        foreach ($mappings as $mapping) {
            DB::table('class_user')
                ->where(
                    'id',
                    $mapping['pivot_id']
                )
                ->where(
                    'class_id',
                    $mapping['old_class_id']
                )
                ->update([
                    'class_id' =>
                        $mapping['new_class_id'],
                    'updated_at' => now(),
                ]);
        }

        /*
         * Vérification finale avant l'ajout de la nouvelle clé.
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
                . 'la conversion : '
                . $details
            );
        }

        /*
         * Nouvelle relation officielle.
         */
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
            'Cette migration convertit des identifiants '
            . 'historiques vers class_rooms. Le rollback '
            . 'automatique est volontairement désactivé.'
        );
    }
};