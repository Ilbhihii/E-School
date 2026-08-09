<?php

namespace App\Services;

use App\Models\ClassSlot;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PedagogicalStructureService
{
    /**
     * Hiérarchie canonique :
     * Matière → Niveau → Classe → Créneau.
     *
     * Les créneaux proviennent de class_slots, qui est désormais
     * la source structurelle commune aux interfaces admin/prof/student.
     */
    public function hierarchyForAdmin(): array
    {
        return ClassSlot::query()
            ->with([
                'subject',
                'level',
                'classRoom',
            ])
            ->where('is_active', true)
            ->orderBy('subject_id')
            ->orderBy('level_id')
            ->orderBy('class_id')
            ->orderBy('position')
            ->orderBy('code')
            ->get()
            ->filter(
                fn (ClassSlot $slot) =>
                    $slot->subject
                    && $slot->level
                    && $slot->classRoom
                    && $this->subjectIsAllowed(
                        $slot->subject
                    )
                    && $this->levelIsAllowed(
                        $slot->subject,
                        $slot->level->name
                    )
            )
            ->groupBy('subject_id')
            ->map(function (Collection $subjectSlots) {
                $first = $subjectSlots->first();

                return [
                    'id' => (int) $first->subject_id,
                    'name' => $first->subject->name,
                    'levels' => $subjectSlots
                        ->groupBy('level_id')
                        ->map(function (Collection $levelSlots) {
                            $firstLevel = $levelSlots->first();

                            return [
                                'id' =>
                                    (int) $firstLevel->level_id,
                                'name' =>
                                    $firstLevel->level->name,
                                'classes' =>
                                    $levelSlots
                                        ->groupBy('class_id')
                                        ->map(function (
                                            Collection $classSlots
                                        ) {
                                            $firstClass =
                                                $classSlots->first();

                                            return [
                                                'id' =>
                                                    (int) $firstClass
                                                        ->class_id,
                                                'name' =>
                                                    $firstClass
                                                        ->classRoom
                                                        ->name,
                                                'slots' =>
                                                    $classSlots
                                                        ->sortBy(
                                                            'position'
                                                        )
                                                        ->map(
                                                            fn (
                                                                ClassSlot $slot
                                                            ) => [
                                                                'id' =>
                                                                    (int) $slot->id,
                                                                'code' =>
                                                                    strtoupper(
                                                                        trim(
                                                                            (string)
                                                                            $slot->code
                                                                        )
                                                                    ),
                                                                'label' =>
                                                                    strtoupper(
                                                                        trim(
                                                                            (string)
                                                                            $slot->code
                                                                        )
                                                                    ),
                                                            ]
                                                        )
                                                        ->unique('id')
                                                        ->values()
                                                        ->all(),
                                            ];
                                        })
                                        ->values()
                                        ->all(),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function slotForPath(
        int $slotId,
        int $subjectId,
        int $levelId,
        int $classId
    ): ClassSlot {
        $slot = ClassSlot::query()
            ->whereKey($slotId)
            ->where('subject_id', $subjectId)
            ->where('level_id', $levelId)
            ->where('class_id', $classId)
            ->where('is_active', true)
            ->first();

        if (!$slot) {
            throw ValidationException::withMessages([
                'class_slot_id' =>
                    'Le créneau sélectionné ne correspond pas '
                    . 'au parcours Matière → Niveau → Classe.',
            ]);
        }

        return $slot;
    }

    public function slotByCodeForPath(
        string $slotCode,
        int $subjectId,
        int $levelId,
        int $classId
    ): ClassSlot {
        $normalized = strtoupper(
            trim($slotCode)
        );

        $slot = ClassSlot::query()
            ->where('subject_id', $subjectId)
            ->where('level_id', $levelId)
            ->where('class_id', $classId)
            ->where('is_active', true)
            ->whereRaw(
                'UPPER(TRIM(code)) = ?',
                [$normalized]
            )
            ->first();

        if (!$slot) {
            throw ValidationException::withMessages([
                'slot_code' =>
                    'Le créneau sélectionné ne correspond pas '
                    . 'au parcours Matière → Niveau → Classe.',
            ]);
        }

        return $slot;
    }

    public function courseMatchesSlot(
        Course $course,
        ClassSlot $slot
    ): bool {
        return
            (int) $course->subject_id
                === (int) $slot->subject_id
            && (int) $course->level_id
                === (int) $slot->level_id
            && (int) $course->class_id
                === (int) $slot->class_id
            && strtoupper(
                trim(
                    (string) $course->slot_code
                )
            )
                === strtoupper(
                    trim(
                        (string) $slot->code
                    )
                );
    }

    public function studentAssignedToSlot(
        int $studentId,
        ClassSlot $slot
    ): bool {
        return DB::table('class_user')
            ->where('user_id', $studentId)
            ->where('subject_id', $slot->subject_id)
            ->where('class_id', $slot->class_id)
            ->where('class_slot_id', $slot->id)
            ->exists();
    }

    private function subjectIsAllowed(
        Subject $subject
    ): bool {
        return in_array(
            $this->normalize(
                $subject->name
            ),
            [
                'arabe',
                'coran',
                'soutien lycee',
            ],
            true
        );
    }

    private function levelIsAllowed(
        Subject $subject,
        string $levelName
    ): bool {
        $subjectName =
            $this->normalize(
                $subject->name
            );

        $allowed = match ($subjectName) {
            'arabe' => [
                'lecture & ecriture',
                'communication',
            ],
            'coran' => [
                'apprentissage & tajwid',
            ],
            'soutien lycee' => [
                'bac',
            ],
            default => [],
        };

        return in_array(
            $this->normalize($levelName),
            $allowed,
            true
        );
    }

    private function normalize(
        string $value
    ): string {
        return Str::lower(
            Str::ascii(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    trim($value)
                )
            )
        );
    }
}
