<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('absences')) {
            return;
        }

        $addSubject = !Schema::hasColumn('absences', 'subject_id');
        $addLevel = !Schema::hasColumn('absences', 'level_id');
        $addClass = !Schema::hasColumn('absences', 'class_id');

        if ($addSubject || $addLevel || $addClass) {
            Schema::table('absences', function (Blueprint $table) use (
                $addSubject,
                $addLevel,
                $addClass
            ) {
                if ($addSubject) {
                    $table->foreignId('subject_id')
                        ->nullable()
                        ->after('user_id')
                        ->constrained('subjects')
                        ->onDelete('set null');
                }

                if ($addLevel) {
                    $table->foreignId('level_id')
                        ->nullable()
                        ->after('subject_id')
                        ->constrained('levels')
                        ->onDelete('set null');
                }

                if ($addClass) {
                    $table->foreignId('class_id')
                        ->nullable()
                        ->after('level_id')
                        ->constrained('class_rooms')
                        ->onDelete('set null');
                }
            });
        }

        $this->backfillExistingAbsences();
    }

    public function down(): void
    {
        if (!Schema::hasTable('absences')) {
            return;
        }

        foreach (['subject_id', 'level_id', 'class_id'] as $column) {
            if (!Schema::hasColumn('absences', $column)) {
                continue;
            }

            $foreignKeys = DB::select(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'absences'
                   AND COLUMN_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL",
                [$column]
            );

            foreach ($foreignKeys as $foreignKey) {
                $name = str_replace('`', '``', $foreignKey->CONSTRAINT_NAME);
                DB::statement("ALTER TABLE `absences` DROP FOREIGN KEY `{$name}`");
            }
        }

        $columns = collect(['subject_id', 'level_id', 'class_id'])
            ->filter(fn ($column) => Schema::hasColumn('absences', $column))
            ->values()
            ->all();

        if (!empty($columns)) {
            Schema::table('absences', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    private function backfillExistingAbsences(): void
    {
        if (
            !Schema::hasTable('class_user')
            || !Schema::hasTable('class_rooms')
            || !Schema::hasTable('levels')
            || !Schema::hasTable('users')
        ) {
            return;
        }

        DB::table('absences')
            ->where(function ($query) {
                $query->whereNull('subject_id')
                    ->orWhereNull('level_id')
                    ->orWhereNull('class_id');
            })
            ->orderBy('id')
            ->chunkById(100, function ($absences) {
                foreach ($absences as $absence) {
                    $path = $this->resolveUniquePath((int) $absence->user_id);

                    if (!$path) {
                        continue;
                    }

                    DB::table('absences')
                        ->where('id', $absence->id)
                        ->update([
                            'subject_id' => $path['subject_id'],
                            'level_id' => $path['level_id'],
                            'class_id' => $path['class_id'],
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    private function resolveUniquePath(int $userId): ?array
    {
        $paths = DB::table('class_user as cu')
            ->join('class_rooms as cr', 'cr.id', '=', 'cu.class_id')
            ->join('levels as l', 'l.id', '=', 'cr.level_id')
            ->where('cu.user_id', $userId)
            ->whereNotNull('cu.subject_id')
            ->whereColumn('l.subject_id', 'cu.subject_id')
            ->select([
                'cu.subject_id',
                'l.id as level_id',
                'cr.id as class_id',
            ])
            ->distinct()
            ->get();

        if ($paths->count() === 1) {
            $path = $paths->first();

            return [
                'subject_id' => (int) $path->subject_id,
                'level_id' => (int) $path->level_id,
                'class_id' => (int) $path->class_id,
            ];
        }

        $primaryClassId = DB::table('users')
            ->where('id', $userId)
            ->value('class_id');

        if ($primaryClassId) {
            $primaryPaths = $paths->where('class_id', (int) $primaryClassId)->values();

            if ($primaryPaths->count() === 1) {
                $path = $primaryPaths->first();

                return [
                    'subject_id' => (int) $path->subject_id,
                    'level_id' => (int) $path->level_id,
                    'class_id' => (int) $path->class_id,
                ];
            }

            $fallback = DB::table('class_rooms as cr')
                ->join('levels as l', 'l.id', '=', 'cr.level_id')
                ->where('cr.id', $primaryClassId)
                ->select([
                    'l.subject_id',
                    'l.id as level_id',
                    'cr.id as class_id',
                ])
                ->first();

            if ($fallback && $fallback->subject_id) {
                return [
                    'subject_id' => (int) $fallback->subject_id,
                    'level_id' => (int) $fallback->level_id,
                    'class_id' => (int) $fallback->class_id,
                ];
            }
        }

        return null;
    }
};
