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
    /**
     * Les 4 premiers groupes restent créés automatiquement pour
     * conserver la compatibilité avec l'existant, mais ce n'est
     * plus une limite métier. D5, D6, I5, A12... sont créés
     * à la demande lorsqu'un horaire étudiant le nécessite.
     */
    public const DEFAULT_SLOT_COUNT = 4;

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
            range(
                1,
                self::DEFAULT_SLOT_COUNT
            )
        )
            ->map(
                fn (int $number) =>
                    $prefix . $number
            )
            ->all();
    }

    /**
     * Synchronise le parcours sans supprimer les groupes dynamiques.
     *
     * Avant : seuls D1-D4 / I1-I4 / A1-A4 restaient actifs.
     * Maintenant : les 4 premiers sont garantis, et tous les groupes
     * supplémentaires déjà créés (D5, D6...) restent actifs.
     */
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

        $prefix = $this->prefixForClass(
            $classRoom->name
        );

        /*
         * Si la classe change réellement de catégorie
         * (ex. Débutant -> Intermédiaire), les anciens groupes
         * d'un autre préfixe restent en historique mais sont désactivés.
         * Les groupes du bon préfixe ne sont jamais limités à 4.
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
            ->where(
                'code',
                'not like',
                $prefix . '%'
            )
            ->update([
                'is_active' => false,
            ]);

        foreach (
            range(
                1,
                self::DEFAULT_SLOT_COUNT
            )
            as $number
        ) {
            $this->ensureSlotForNumber(
                $subject,
                $level,
                $classRoom,
                $number
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
            ->where(
                'code',
                'like',
                $prefix . '%'
            )
            ->where(
                'is_active',
                true
            )
            ->orderBy(
                'position'
            )
            ->orderBy(
                'code'
            )
            ->get();
    }

    /**
     * Retourne/crée le groupe correspondant au rang horaire.
     *
     * Exemples pour une classe Débutant :
     * 1 -> D1
     * 2 -> D2
     * 3 -> D3
     * 27 -> D27
     *
     * Il n'existe plus de plafond applicatif D1-D4.
     */
    public function ensureSlotForNumber(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom,
        int $number
    ): ClassSlot {
        $this->assertPath(
            $subject,
            $level,
            $classRoom
        );

        if ($number < 1) {
            throw ValidationException::withMessages([
                'schedule_id' =>
                    'Le numéro de groupe calculé est invalide.',
            ]);
        }

        $prefix = $this->prefixForClass(
            $classRoom->name
        );

        $code =
            $prefix
            . $number;

        return ClassSlot::query()
            ->updateOrCreate(
                [
                    'subject_id' =>
                        $subject->id,
                    'level_id' =>
                        $level->id,
                    'class_id' =>
                        $classRoom->id,
                    'code' =>
                        $code,
                ],
                [
                    'position' =>
                        $number,
                    'is_active' =>
                        true,
                ]
            );
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
            ->where(
                'is_active',
                true
            )
            ->first();
    }

    /**
     * Préfixe du groupe pédagogique selon la classe.
     */
    public function prefixForClassName(
        string $name
    ): string {
        return $this->prefixForClass(
            $name
        );
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
                trim(
                    $value
                )
            )
        );
    }
}
