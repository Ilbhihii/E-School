<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\ClassSlot;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClassSlotService
{
    public const SLOT_COUNT = 4;

    public function codesForClass(
        ClassRoom $classRoom
    ): array {
        return $this->codesForClassName(
            $classRoom->name
        );
    }

    public function codesForClassName(
        string $className
    ): array {
        $prefix = $this->prefixForClass(
            $className
        );

        return collect(
            range(1, self::SLOT_COUNT)
        )
            ->map(
                fn (int $number) =>
                    $prefix . $number
            )
            ->all();
    }

    public function syncForPath(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom
    ): Collection {
        $this->assertPath(
            $subject,
            $level,
            $classRoom
        );

        $codes = $this->codesForClass(
            $classRoom
        );

        /*
         * Si la classe a été renommée et que son préfixe change
         * (ex. Débutant D1-D4 → Avancé A1-A4), les anciens
         * créneaux sont conservés pour l'historique mais désactivés.
         * Il reste donc exactement 4 créneaux ACTIFS.
         */
        ClassSlot::query()
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'level_id',
                $level->id
            )
            ->where(
                'class_id',
                $classRoom->id
            )
            ->whereNotIn(
                'code',
                $codes
            )
            ->update([
                'is_active' => false,
            ]);

        foreach ($codes as $index => $code) {
            ClassSlot::query()->updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'level_id' => $level->id,
                    'class_id' => $classRoom->id,
                    'code' => $code,
                ],
                [
                    'position' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        return ClassSlot::query()
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'level_id',
                $level->id
            )
            ->where(
                'class_id',
                $classRoom->id
            )
            ->whereIn(
                'code',
                $codes
            )
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('code')
            ->get();
    }

    public function slotForPath(
        int $slotId,
        int $subjectId,
        int $levelId,
        int $classId
    ): ?ClassSlot {
        return ClassSlot::query()
            ->whereKey($slotId)
            ->where(
                'subject_id',
                $subjectId
            )
            ->where(
                'level_id',
                $levelId
            )
            ->where(
                'class_id',
                $classId
            )
            ->where('is_active', true)
            ->first();
    }

    private function prefixForClass(
        string $name
    ): string {
        $normalized = $this->normalize(
            $name
        );

        return match (true) {
            str_contains(
                $normalized,
                'debutant'
            ) => 'D',

            str_contains(
                $normalized,
                'intermediaire'
            ) => 'I',

            str_contains(
                $normalized,
                'avance'
            ) => 'A',

            default => 'G',
        };
    }

    private function assertPath(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom
    ): void {
        if (
            (int) $level->subject_id
            !== (int) $subject->id
            || (int) $classRoom->level_id
            !== (int) $level->id
            || !$classRoom
                ->subjects()
                ->where(
                    'subjects.id',
                    $subject->id
                )
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'class_id' =>
                    'Cette classe n’appartient pas au parcours sélectionné.',
            ]);
        }
    }

    private function normalize(
        string $value
    ): string {
        return Str::lower(
            Str::ascii(
                trim($value)
            )
        );
    }
}
