<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Corrige l'ancienne clé étrangère class_user.class_id qui pointait vers
     * `classes` afin qu'elle pointe vers `class_rooms`.
     *
     * La migration reste compatible avec la base historique de production,
     * tout en pouvant s'exécuter sur une installation neuve où les IDs
     * historiques 39/40 et 201/202 n'existent pas.
     */
    public function up(): void
    {
        if (
            !Schema::hasTable('class_user')
            || !Schema::hasTable('class_rooms')
        ) {
            return;
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
                fn ($foreignKey) =>
                    $foreignKey->REFERENCED_TABLE_NAME
                    === 'class_rooms'
            );

        if ($alreadyCorrect) {
            return;
        }

        /*
         * Correspondances historiques connues sur l'ancienne base :
         * classes.id 39 -> class_rooms.id 202 (Avancé)
         * classes.id 40 -> class_rooms.id 201 (Intermédiaire)
         *
         * On ne vérifie ni n'utilise ces IDs si aucune ligne class_user ne
         * les référence. Cela rend la migration sûre sur une base neuve.
         */
        $mappings = [
            39 => 202,
            40 => 201,
        ];

        $knownRows = DB::table('class_user')
            ->whereIn('class_id', array_keys($mappings))
            ->get();

        if ($knownRows->isNotEmpty()) {
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

            foreach ($knownRows as $row) {
                $oldId = (int) $row->class_id;
                $newId = $mappings[$oldId] ?? null;
                $target = $newId
                    ? $targets->get($newId)
                    : null;

                if (!$target) {
                    throw new \RuntimeException(
                        'Impossible de convertir class_user.class_id='
                        . $oldId
                        . ' : la classe cible class_rooms.id='
                        . ($newId ?? 'NULL')
                        . ' est introuvable.'
                    );
                }

                // La vérification historique du parcours n'est appliquée que
                // lorsque le sujet existe réellement dans la ligne concernée.
                if (
                    Schema::hasColumn('class_user', 'subject_id')
                    && (int) ($row->subject_id ?? 0) === 10
                    && (
                        (int) $target->subject_id !== 10
                        || trim((string) $target->level_name)
                            !== 'Apprentissage & Tajwid'
                    )
                ) {
                    throw new \RuntimeException(
                        'La classe cible '
                        . $newId
                        . ' ne correspond pas au parcours historique '
                        . 'Coran → Apprentissage & Tajwid.'
                    );
                }
            }
        }

        // Refuser toute autre référence invalide pour éviter une perte de
        // données silencieuse lors du changement de clé étrangère.
        $unexpectedInvalidRows = DB::table('class_user as cu')
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
                    fn ($row) => sprintf(
                        '#%s user=%s class=%s subject=%s',
                        $row->id,
                        $row->user_id,
                        $row->class_id,
                        $row->subject_id ?? 'NULL'
                    )
                )
                ->implode('; ');

            throw new \RuntimeException(
                'D’autres class_id invalides ont été détectés. '
                . 'Aucune donnée n’a été supprimée. Détails : '
                . $details
            );
        }

        // Les validations sont terminées : on peut retirer l'ancienne clé.
        foreach ($foreignKeys as $foreignKey) {
            $constraintName = str_replace(
                '`',
                '``',
                $foreignKey->CONSTRAINT_NAME
            );

            DB::statement(
                "ALTER TABLE `class_user` DROP FOREIGN KEY `"
                . $constraintName
                . "`"
            );
        }

        // Convertir uniquement les lignes historiques réellement présentes.
        foreach ($mappings as $oldId => $newId) {
            $query = DB::table('class_user')
                ->where('class_id', $oldId);

            if (Schema::hasColumn('class_user', 'subject_id')) {
                $query->where('subject_id', 10);
            }

            $query->update([
                'class_id' => $newId,
                'updated_at' => now(),
            ]);
        }

        $remainingInvalidRows = DB::table('class_user as cu')
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
                    fn ($row) => sprintf(
                        '#%s user=%s class=%s subject=%s',
                        $row->id,
                        $row->user_id,
                        $row->class_id,
                        $row->subject_id ?? 'NULL'
                    )
                )
                ->implode('; ');

            throw new \RuntimeException(
                'Des class_id invalides restent après la conversion. '
                . 'Détails : '
                . $details
            );
        }

        DB::statement(
            "
                ALTER TABLE `class_user`
                ADD CONSTRAINT `class_user_class_id_foreign`
                FOREIGN KEY (`class_id`)
                REFERENCES `class_rooms` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            "
        );
    }

    /**
     * Le rollback retire uniquement la clé étrangère corrigée.
     * Les anciens identifiants historiques ne sont pas restaurés, car cela
     * pourrait réintroduire des références invalides ou perdre des données.
     */
    public function down(): void
    {
        if (!Schema::hasTable('class_user')) {
            return;
        }

        $foreignKeys = DB::select(
            "
                SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'class_user'
                  AND COLUMN_NAME = 'class_id'
                  AND REFERENCED_TABLE_NAME = 'class_rooms'
            "
        );

        foreach ($foreignKeys as $foreignKey) {
            $constraintName = str_replace(
                '`',
                '``',
                $foreignKey->CONSTRAINT_NAME
            );

            DB::statement(
                "ALTER TABLE `class_user` DROP FOREIGN KEY `"
                . $constraintName
                . "`"
            );
        }
    }
};
