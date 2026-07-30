<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SUBJECT_NAME = 'Soutien Lycée';
    private const LEVEL_NAME = 'BAC';

    private const SUBJECTS = [
        'Mathématiques',
        'Physique-Chimie',
    ];

    public function up(): void
    {
        if (
            !Schema::hasTable('subjects')
            || !Schema::hasTable('levels')
            || !Schema::hasTable('class_rooms')
            || !Schema::hasTable('class_room_subject')
        ) {
            return;
        }

        $subjectId = $this->upsertSubject();
        $levelId = $this->upsertLevel($subjectId);

        foreach (self::SUBJECTS as $subjectName) {
            $classRoomId = $this->upsertClassRoom(
                $levelId,
                $subjectName
            );

            $this->attachSubject(
                $classRoomId,
                $subjectId
            );
        }
    }

    public function down(): void
    {
        /*
         * La structure n'est pas supprimée automatiquement afin d'éviter
         * de perdre des cours, des lives ou des inscriptions qui pourraient
         * être ajoutés après le déploiement.
         */
    }

    private function upsertSubject(): int
    {
        $existingId = DB::table('subjects')
            ->where('name', self::SUBJECT_NAME)
            ->value('id');

        $values = [
            'type' => 'scolaire',
        ];

        if (Schema::hasColumn('subjects', 'description')) {
            $values['description'] =
                'Soutien scolaire destiné aux élèves du lycée et du '
                . 'baccalauréat en Mathématiques et Physique-Chimie.';
        }

        if (Schema::hasColumn('subjects', 'image')) {
            $values['image'] = null;
        }

        if (Schema::hasColumn('subjects', 'updated_at')) {
            $values['updated_at'] = now();
        }

        if ($existingId) {
            DB::table('subjects')
                ->where('id', $existingId)
                ->update($values);

            return (int) $existingId;
        }

        if (Schema::hasColumn('subjects', 'created_at')) {
            $values['created_at'] = now();
        }

        $values['name'] = self::SUBJECT_NAME;

        return (int) DB::table('subjects')
            ->insertGetId($values);
    }

    private function upsertLevel(int $subjectId): int
    {
        $existingId = DB::table('levels')
            ->where('subject_id', $subjectId)
            ->where('name', self::LEVEL_NAME)
            ->value('id');

        $values = [];

        if (Schema::hasColumn('levels', 'description')) {
            $values['description'] =
                'Préparation et accompagnement pour le baccalauréat.';
        }

        if (Schema::hasColumn('levels', 'order')) {
            $values['order'] = 1;
        }

        if (Schema::hasColumn('levels', 'updated_at')) {
            $values['updated_at'] = now();
        }

        if ($existingId) {
            DB::table('levels')
                ->where('id', $existingId)
                ->update($values);

            return (int) $existingId;
        }

        if (Schema::hasColumn('levels', 'created_at')) {
            $values['created_at'] = now();
        }

        $values['subject_id'] = $subjectId;
        $values['name'] = self::LEVEL_NAME;

        return (int) DB::table('levels')
            ->insertGetId($values);
    }

    private function upsertClassRoom(
        int $levelId,
        string $name
    ): int {
        $existingId = DB::table('class_rooms')
            ->where('level_id', $levelId)
            ->where('name', $name)
            ->value('id');

        $values = [];

        if (Schema::hasColumn('class_rooms', 'updated_at')) {
            $values['updated_at'] = now();
        }

        if ($existingId) {
            if (!empty($values)) {
                DB::table('class_rooms')
                    ->where('id', $existingId)
                    ->update($values);
            }

            return (int) $existingId;
        }

        if (Schema::hasColumn('class_rooms', 'created_at')) {
            $values['created_at'] = now();
        }

        $values['level_id'] = $levelId;
        $values['name'] = $name;

        return (int) DB::table('class_rooms')
            ->insertGetId($values);
    }

    private function attachSubject(
        int $classRoomId,
        int $subjectId
    ): void {
        $exists = DB::table('class_room_subject')
            ->where('class_room_id', $classRoomId)
            ->where('subject_id', $subjectId)
            ->exists();

        if ($exists) {
            return;
        }

        $values = [
            'class_room_id' => $classRoomId,
            'subject_id' => $subjectId,
        ];

        if (
            Schema::hasColumn(
                'class_room_subject',
                'created_at'
            )
        ) {
            $values['created_at'] = now();
        }

        if (
            Schema::hasColumn(
                'class_room_subject',
                'updated_at'
            )
        ) {
            $values['updated_at'] = now();
        }

        DB::table('class_room_subject')->insert($values);
    }
};
