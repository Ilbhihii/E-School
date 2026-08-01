<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Level;
use App\Models\ProfAssignment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LearningPathService
{
    public function hierarchyForAdmin(): array
    {
        return $this->buildHierarchy(
            Subject::query()
                ->orderBy('name')
                ->get(),
            Level::query()
                ->with([
                    'classes.subjects',
                ])
                ->orderBy('order')
                ->orderBy('name')
                ->get()
        );
    }

    public function hierarchyForProfessor(
        int $professorId
    ): array {
        $scope = ProfAssignment::query()
            ->where(
                'prof_id',
                $professorId
            )
            ->get();

        if ($scope->isEmpty()) {
            return [];
        }

        $subjects = Subject::query()
            ->whereIn(
                'id',
                $scope->pluck('subject_id')
            )
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->whereIn(
                'id',
                $scope->pluck('level_id')
            )
            ->with([
                'classes.subjects',
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return $this->buildHierarchy(
            $subjects,
            $levels,
            function (
                Subject $subject,
                Level $level,
                ClassRoom $class
            ) use ($scope) {
                return $scope->contains(
                    function (
                        $assignment
                    ) use (
                        $subject,
                        $level,
                        $class
                    ) {
                        return
                            (int) $assignment
                                ->subject_id
                            === (int) $subject->id
                            && (int) $assignment
                                ->level_id
                            === (int) $level->id
                            && (int) $assignment
                                ->class_id
                            === (int) $class->id;
                    }
                );
            }
        );
    }

    public function hierarchyForStudent(
        int $studentId
    ): array {
        $rows =
            $this->studentAssignmentRows(
                $studentId
            );

        if ($rows->isEmpty()) {
            return [];
        }

        $student = User::find($studentId);

        $subjects = Subject::query()
            ->whereIn(
                'id',
                $rows->pluck('subject_id')
            )
            ->orderBy('name')
            ->get();

        if (
            $student
            && $this->hasSoutienLyceeOnlyPlan(
                $student
            )
        ) {
            $subjects = $subjects
                ->filter(
                    function (
                        Subject $subject
                    ) {
                        return $this
                            ->isSoutienLyceeSubject(
                                $subject
                            );
                    }
                )
                ->values();
        }

        $allowedSubjectIds =
            $subjects->pluck('id');

        $rows = $rows
            ->whereIn(
                'subject_id',
                $allowedSubjectIds
            )
            ->values();

        if ($rows->isEmpty()) {
            return [];
        }

        $levels = Level::query()
            ->whereIn(
                'id',
                $rows->pluck('level_id')
            )
            ->with([
                'classes.subjects',
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return $this->buildHierarchy(
            $subjects,
            $levels,
            function (
                Subject $subject,
                Level $level,
                ClassRoom $class
            ) use ($rows) {
                return $rows->contains(
                    function (
                        $row
                    ) use (
                        $subject,
                        $level,
                        $class
                    ) {
                        return
                            (int) $row->subject_id
                            === (int) $subject->id
                            && (int) $row->level_id
                            === (int) $level->id
                            && (int) $row->class_id
                            === (int) $class->id;
                    }
                );
            }
        );
    }

    public function studentAssignmentRows(
        int $studentId
    ): Collection {
        return DB::table('class_user')
            ->join(
                'class_rooms',
                'class_user.class_id',
                '=',
                'class_rooms.id'
            )
            ->join(
                'levels',
                'class_rooms.level_id',
                '=',
                'levels.id'
            )
            ->where(
                'class_user.user_id',
                $studentId
            )
            ->whereNotNull(
                'class_user.subject_id'
            )
            ->select([
                'class_user.id',
                'class_user.user_id',
                'class_user.subject_id',
                'class_user.class_id',
                'class_rooms.level_id',
            ])
            ->get()
            ->unique(
                function ($row) {
                    return
                        $row->subject_id
                        . ':'
                        . $row->level_id
                        . ':'
                        . $row->class_id;
                }
            )
            ->values();
    }

    /**
     * Indique si l'étudiant possède au moins une assignation
     * Soutien Lycée → BAC → classe.
     *
     * Cette méthode sert notamment à afficher ou masquer
     * l'entrée « Mes tests BAC » dans le menu étudiant.
     */
    public function studentHasSoutienLyceeBacPath(
        int $studentId
    ): bool {
        $rows = $this->studentAssignmentRows(
            $studentId
        );

        if ($rows->isEmpty()) {
            return false;
        }

        $subjects = Subject::query()
            ->whereIn(
                'id',
                $rows->pluck('subject_id')
            )
            ->get()
            ->keyBy('id');

        $levels = Level::query()
            ->whereIn(
                'id',
                $rows->pluck('level_id')
            )
            ->get()
            ->keyBy('id');

        foreach ($rows as $row) {
            $subject = $subjects->get(
                (int) $row->subject_id
            );

            $level = $levels->get(
                (int) $row->level_id
            );

            if (!$subject || !$level) {
                continue;
            }

            $subjectName = Str::lower(
                Str::ascii(
                    trim(
                        (string) $subject->name
                    )
                )
            );

            $levelName = Str::lower(
                Str::ascii(
                    trim(
                        (string) $level->name
                    )
                )
            );

            if (
                $subjectName === 'soutien lycee'
                && $levelName === 'bac'
            ) {
                return true;
            }
        }

        return false;
    }

    public function validatePath(
        int $subjectId,
        int $levelId,
        int $classId
    ): array {
        $subject =
            Subject::findOrFail($subjectId);

        $level = Level::query()
            ->whereKey($levelId)
            ->where(
                'subject_id',
                $subject->id
            )
            ->first();

        if (!$level) {
            throw ValidationException
                ::withMessages([
                    'level_id' =>
                        'Le niveau sélectionné '
                        . 'n’appartient pas '
                        . 'à cette matière.',
                ]);
        }

        $class = ClassRoom::query()
            ->whereKey($classId)
            ->where(
                'level_id',
                $level->id
            )
            ->whereHas(
                'subjects',
                function (
                    $query
                ) use ($subject) {
                    $query->where(
                        'subjects.id',
                        $subject->id
                    );
                }
            )
            ->first();

        if (!$class) {
            throw ValidationException
                ::withMessages([
                    'class_id' =>
                        'La classe sélectionnée '
                        . 'n’appartient pas '
                        . 'à ce niveau et '
                        . 'à cette matière.',
                ]);
        }

        return [
            $subject,
            $level,
            $class,
        ];
    }

    public function studentCanAccessPath(
        User $student,
        int $subjectId,
        int $levelId,
        int $classId
    ): bool {
        if (!$student->isStudent()) {
            return false;
        }

        if (
            !$this->subscriptionAllowsSubject(
                $student,
                $subjectId
            )
        ) {
            return false;
        }

        return $this
            ->studentAssignmentRows(
                $student->id
            )
            ->contains(
                function (
                    $row
                ) use (
                    $subjectId,
                    $levelId,
                    $classId
                ) {
                    return
                        (int) $row->subject_id
                        === $subjectId
                        && (int) $row->level_id
                        === $levelId
                        && (int) $row->class_id
                        === $classId;
                }
            );
    }

    public function studentCanAccessCourse(
        User $student,
        Course $course
    ): bool {
        if (
            !$course->subject_id
            || !$course->level_id
            || !$course->class_id
        ) {
            return false;
        }

        return $this->studentCanAccessPath(
            $student,
            (int) $course->subject_id,
            (int) $course->level_id,
            (int) $course->class_id
        );
    }

    public function professorCanAccessPath(
        User $professor,
        int $subjectId,
        int $levelId,
        int $classId
    ): bool {
        if (!$professor->isProf()) {
            return false;
        }

        return ProfAssignment::query()
            ->where(
                'prof_id',
                $professor->id
            )
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
            ->exists();
    }

    public function professorCanAccessClassLevel(
        User $professor,
        int $levelId,
        int $classId
    ): bool {
        return $professor->isProf()
            && ProfAssignment::query()
                ->where(
                    'prof_id',
                    $professor->id
                )
                ->where(
                    'level_id',
                    $levelId
                )
                ->where(
                    'class_id',
                    $classId
                )
                ->exists();
    }

    public function professorCanAccessCourse(
        User $professor,
        Course $course
    ): bool {
        return $this->professorCanAccessPath(
            $professor,
            (int) $course->subject_id,
            (int) $course->level_id,
            (int) $course->class_id
        );
    }

    public function professorCanAccessStudent(
        User $professor,
        int $studentId,
        int $subjectId,
        int $levelId,
        int $classId
    ): bool {
        if (
            !$this->professorCanAccessPath(
                $professor,
                $subjectId,
                $levelId,
                $classId
            )
        ) {
            return false;
        }

        return DB::table('class_user')
            ->where(
                'user_id',
                $studentId
            )
            ->where(
                'subject_id',
                $subjectId
            )
            ->where(
                'class_id',
                $classId
            )
            ->exists();
    }

    public function userCanAccessCourse(
        ?User $user,
        Course $course
    ): bool {
        if ((bool) $course->is_free) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isProf()) {
            return
                (int) $course->user_id
                === (int) $user->id
                || $this
                    ->professorCanAccessCourse(
                        $user,
                        $course
                    );
        }

        return
            $user->isStudent()
            && (bool) $user->is_active
            && (bool) $user->is_paid
            && $this
                ->studentCanAccessCourse(
                    $user,
                    $course
                );
    }

    private function subscriptionAllowsSubject(
        User $student,
        int $subjectId
    ): bool {
        if (
            !$this->hasSoutienLyceeOnlyPlan(
                $student
            )
        ) {
            return true;
        }

        $subject = Subject::find($subjectId);

        return $subject
            && $this->isSoutienLyceeSubject(
                $subject
            );
    }

    private function hasSoutienLyceeOnlyPlan(
        User $student
    ): bool {
        return
            (string) $student
                ->subscription_type
            === 'soutien_lycee';
    }

    private function isSoutienLyceeSubject(
        Subject $subject
    ): bool {
        $normalized = Str::lower(
            Str::ascii(
                trim($subject->name)
            )
        );

        return $normalized
            === 'soutien lycee';
    }

    private function buildHierarchy(
        Collection $subjects,
        Collection $levels,
        callable $scope = null
    ): array {
        return $subjects
            ->map(
                function (
                    Subject $subject
                ) use (
                    $levels,
                    $scope
                ) {
                    $subjectLevels =
                        $levels
                            ->where(
                                'subject_id',
                                $subject->id
                            )
                            ->map(
                                function (
                                    Level $level
                                ) use (
                                    $subject,
                                    $scope
                                ) {
                                    $classes =
                                        $level
                                            ->classes
                                            ->filter(
                                                function (
                                                    ClassRoom $class
                                                ) use (
                                                    $subject,
                                                    $level,
                                                    $scope
                                                ) {
                                                    $linked =
                                                        $class
                                                            ->subjects
                                                            ->contains(
                                                                'id',
                                                                $subject
                                                                    ->id
                                                            );

                                                    return
                                                        $linked
                                                        && (
                                                            !$scope
                                                            || $scope(
                                                                $subject,
                                                                $level,
                                                                $class
                                                            )
                                                        );
                                                }
                                            )
                                            ->sortBy('name')
                                            ->unique('id')
                                            ->values()
                                            ->map(
                                                function (
                                                    ClassRoom $class
                                                ) {
                                                    return [
                                                        'id' =>
                                                            $class->id,
                                                        'name' =>
                                                            $class->name,
                                                    ];
                                                }
                                            )
                                            ->all();

                                    if (
                                        empty($classes)
                                    ) {
                                        return null;
                                    }

                                    return [
                                        'id' =>
                                            $level->id,
                                        'name' =>
                                            $level->name,
                                        'classes' =>
                                            $classes,
                                    ];
                                }
                            )
                            ->filter()
                            ->values()
                            ->all();

                    if (
                        empty($subjectLevels)
                    ) {
                        return null;
                    }

                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'levels' =>
                            $subjectLevels,
                    ];
                }
            )
            ->filter()
            ->values()
            ->all();
    }
}
