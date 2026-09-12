<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\StudentBehaviorNote;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BehaviorNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request)
            ->with([
                'professor:id,name,email',
                'student:id,name,email',
                'subject:id,name',
                'level:id,name',
                'classRoom:id,name',
            ])
            ->orderByDesc('noted_at')
            ->orderByDesc('id');

        $notes = $query
            ->paginate(30)
            ->withQueryString();

        $statsQuery = $this->filteredQuery($request);

        $summary = [
            'notes_count' =>
                (clone $statsQuery)->count(),

            'positive_points' =>
                (int) (clone $statsQuery)
                    ->where(
                        'type',
                        StudentBehaviorNote::TYPE_POSITIVE
                    )
                    ->sum('points'),

            'negative_points' =>
                (int) (clone $statsQuery)
                    ->where(
                        'type',
                        StudentBehaviorNote::TYPE_NEGATIVE
                    )
                    ->sum('points'),

            'students_count' =>
                (clone $statsQuery)
                    ->distinct()
                    ->count('student_id'),

            'professors_count' =>
                (clone $statsQuery)
                    ->distinct()
                    ->count('professor_id'),
        ];

        $summary['balance'] =
            $summary['positive_points']
            - $summary['negative_points'];

        $professorIds = StudentBehaviorNote::query()
            ->distinct()
            ->pluck('professor_id');

        $studentIds = StudentBehaviorNote::query()
            ->distinct()
            ->pluck('student_id');

        $subjectIds = StudentBehaviorNote::query()
            ->distinct()
            ->pluck('subject_id');

        $levelIds = StudentBehaviorNote::query()
            ->distinct()
            ->pluck('level_id');

        $classIds = StudentBehaviorNote::query()
            ->distinct()
            ->pluck('class_room_id');

        $professors = User::query()
            ->whereIn('id', $professorIds)
            ->where('role', User::ROLE_PROF)
            ->orderBy('name')
            ->get(['id', 'name']);

        $students = User::query()
            ->whereIn('id', $studentIds)
            ->where('role', User::ROLE_STUDENT)
            ->orderBy('name')
            ->get(['id', 'name']);

        $subjects = Subject::query()
            ->whereIn('id', $subjectIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $levels = Level::query()
            ->whereIn('id', $levelIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $classes = ClassRoom::query()
            ->whereIn('id', $classIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            'admin.behavior-notes.index',
            compact(
                'notes',
                'summary',
                'professors',
                'students',
                'subjects',
                'levels',
                'classes'
            )
        );
    }

    public function student(
        User $student
    ) {
        abort_unless(
            $student->role === User::ROLE_STUDENT,
            404
        );

        $notes = StudentBehaviorNote::query()
            ->with([
                'professor:id,name,email',
                'subject:id,name',
                'level:id,name',
                'classRoom:id,name',
            ])
            ->where(
                'student_id',
                $student->id
            )
            ->orderByDesc('noted_at')
            ->orderByDesc('id')
            ->paginate(40);

        $positivePoints =
            (int) StudentBehaviorNote::query()
                ->where(
                    'student_id',
                    $student->id
                )
                ->where(
                    'type',
                    StudentBehaviorNote::TYPE_POSITIVE
                )
                ->sum('points');

        $negativePoints =
            (int) StudentBehaviorNote::query()
                ->where(
                    'student_id',
                    $student->id
                )
                ->where(
                    'type',
                    StudentBehaviorNote::TYPE_NEGATIVE
                )
                ->sum('points');

        $summary = [
            'notes_count' =>
                StudentBehaviorNote::query()
                    ->where(
                        'student_id',
                        $student->id
                    )
                    ->count(),

            'positive_points' =>
                $positivePoints,

            'negative_points' =>
                $negativePoints,

            'balance' =>
                $positivePoints
                - $negativePoints,
        ];

        return view(
            'admin.behavior-notes.student',
            compact(
                'student',
                'notes',
                'summary'
            )
        );
    }

    private function filteredQuery(
        Request $request
    ): Builder {
        $query =
            StudentBehaviorNote::query();

        if ($request->filled('search')) {
            $search =
                trim((string) $request->input('search'));

            $query->where(
                function (Builder $builder) use ($search) {
                    $builder
                        ->where(
                            'note',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhereHas(
                            'student',
                            function (Builder $studentQuery) use ($search) {
                                $studentQuery
                                    ->where(
                                        'name',
                                        'like',
                                        '%' . $search . '%'
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        '%' . $search . '%'
                                    );
                            }
                        )
                        ->orWhereHas(
                            'professor',
                            function (Builder $professorQuery) use ($search) {
                                $professorQuery
                                    ->where(
                                        'name',
                                        'like',
                                        '%' . $search . '%'
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        '%' . $search . '%'
                                    );
                            }
                        );
                }
            );
        }

        $this->applyIntegerFilter(
            $query,
            $request,
            'professor_id'
        );

        $this->applyIntegerFilter(
            $query,
            $request,
            'student_id'
        );

        $this->applyIntegerFilter(
            $query,
            $request,
            'subject_id'
        );

        $this->applyIntegerFilter(
            $query,
            $request,
            'level_id'
        );

        $this->applyIntegerFilter(
            $query,
            $request,
            'class_room_id'
        );

        if (
            $request->filled('type')
            && in_array(
                $request->input('type'),
                [
                    StudentBehaviorNote::TYPE_POSITIVE,
                    StudentBehaviorNote::TYPE_NEGATIVE,
                ],
                true
            )
        ) {
            $query->where(
                'type',
                $request->input('type')
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'noted_at',
                '>=',
                $request->input('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'noted_at',
                '<=',
                $request->input('date_to')
            );
        }

        return $query;
    }

    private function applyIntegerFilter(
        Builder $query,
        Request $request,
        string $column
    ): void {
        if (!$request->filled($column)) {
            return;
        }

        $value =
            filter_var(
                $request->input($column),
                FILTER_VALIDATE_INT
            );

        if ($value === false) {
            return;
        }

        $query->where(
            $column,
            (int) $value
        );
    }
}
