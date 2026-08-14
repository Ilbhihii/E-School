<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Assignment;
use App\Models\HighSchoolTestSubmission;
use App\Models\Result;
use App\Models\Schedule;
use App\Models\User;
use App\Models\VocalTestSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    public function dashboard()
    {
        $children = $this->childrenForParent(auth()->id());

        $cards = $children->map(function (User $student) {
            return (object) [
                'student' => $student,
                'paths' => $this->studentPaths($student->id),
                'absences' => Absence::where('user_id', $student->id)
                    ->where('present', false)->count(),
                'pending_assignments' => Assignment::where('user_id', $student->id)
                    ->whereNull('grade')->count(),
                'results' => $this->resultsCount($student->id),
            ];
        });

        return view('parent.dashboard', compact('children', 'cards'));
    }

    public function show(User $student)
    {
        $link = $this->parentLink($student);
        $paths = $this->studentPaths($student->id);

        $recentAbsences = (bool) $link->can_view_absences
            ? Absence::with(['subject', 'level', 'classRoom', 'classSlot'])
                ->where('user_id', $student->id)->latest('date')->limit(5)->get()
            : collect();

        $recentAssignments = (bool) $link->can_view_assignments
            ? Assignment::with(['subject', 'course', 'classSlot'])
                ->where('user_id', $student->id)->latest('id')->limit(5)->get()
            : collect();

        $summary = (object) [
            'presence_count' => Absence::where('user_id', $student->id)
                ->where('present', true)->count(),
            'absence_count' => Absence::where('user_id', $student->id)
                ->where('present', false)->count(),
            'pending_assignments' => Assignment::where('user_id', $student->id)
                ->whereNull('grade')->count(),
            'results_count' => $this->resultsCount($student->id),
        ];

        return view('parent.children.show', compact(
            'student', 'link', 'paths', 'recentAbsences', 'recentAssignments', 'summary'
        ));
    }

    public function schedule(User $student)
    {
        $this->parentLink($student, 'can_view_schedule');
        $schedules = $this->schedulesForChild($student);

        return view('parent.children.schedule', compact('student', 'schedules'));
    }

    public function absences(User $student)
    {
        $this->parentLink($student, 'can_view_absences');

        $absences = Absence::with(['subject', 'level', 'classRoom', 'classSlot'])
            ->where('user_id', $student->id)->latest('date')->paginate(20);

        return view('parent.children.absences', compact('student', 'absences'));
    }

    public function assignments(User $student)
    {
        $this->parentLink($student, 'can_view_assignments');

        $assignments = Assignment::with(['subject', 'course', 'classSlot'])
            ->where('user_id', $student->id)
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->paginate(20);

        return view('parent.children.assignments', compact('student', 'assignments'));
    }

    public function results(User $student)
    {
        $this->parentLink($student, 'can_view_results');

        $qcmResults = Result::with('test.subject')
            ->where('user_id', $student->id)->latest()->get();

        $vocalResults = VocalTestSubmission::with(['subject', 'level', 'classRoom'])
            ->where('user_id', $student->id)
            ->where(function ($query) {
                $query->whereNotNull('final_score')->orWhereNotNull('score');
            })
            ->latest('submitted_at')->get();

        $writtenResults = HighSchoolTestSubmission::with(['subject', 'level', 'classRoom'])
            ->where('user_id', $student->id)
            ->whereNotNull('score')
            ->latest('submitted_at')->get();

        return view('parent.children.results', compact(
            'student', 'qcmResults', 'vocalResults', 'writtenResults'
        ));
    }

    private function childrenForParent(int $parentId): Collection
    {
        return User::query()
            ->select([
                'users.*',
                'parent_student.relationship as parent_relationship',
                'parent_student.is_primary as parent_is_primary',
            ])
            ->join('parent_student', 'parent_student.student_id', '=', 'users.id')
            ->where('parent_student.parent_id', $parentId)
            ->where('users.role', 'student')
            ->orderByDesc('parent_student.is_primary')
            ->orderBy('users.name')
            ->get();
    }

    private function parentLink(User $student, ?string $permission = null)
    {
        abort_unless($student->role === 'student', 404);

        $link = DB::table('parent_student')
            ->where('parent_id', auth()->id())
            ->where('student_id', $student->id)
            ->first();

        abort_unless($link, 403, 'Cet enfant n’est pas associé à votre compte.');

        if ($permission !== null) {
            abort_unless(
                isset($link->{$permission}) && (bool) $link->{$permission},
                403,
                'Vous n’avez pas l’autorisation de consulter cette rubrique.'
            );
        }

        return $link;
    }

    private function studentPaths(int $studentId): Collection
    {
        return DB::table('class_user as cu')
            ->leftJoin('subjects as s', 's.id', '=', 'cu.subject_id')
            ->leftJoin('class_rooms as cr', 'cr.id', '=', 'cu.class_id')
            ->leftJoin('levels as l', 'l.id', '=', 'cr.level_id')
            ->leftJoin('class_slots as cs', 'cs.id', '=', 'cu.class_slot_id')
            ->where('cu.user_id', $studentId)
            ->where(function ($query) {
                $query->whereNull('s.status')->orWhere('s.status', 'active');
            })
            ->select([
                'cu.subject_id',
                'cu.class_id',
                's.name as subject_name',
                'l.id as level_id',
                'l.name as level_name',
                'cr.name as class_name',
                'cs.code as slot_code',
            ])
            ->orderBy('s.name')
            ->get()
            ->unique(function ($row) {
                return implode(':', [
                    $row->subject_id,
                    $row->level_id,
                    $row->class_id,
                    $row->slot_code,
                ]);
            })
            ->values();
    }

    private function schedulesForChild(User $student): Collection
    {
        $paths = $this->studentPaths($student->id);

        if ($paths->isEmpty()) {
            return collect();
        }

        $query = Schedule::with(['subjectModel', 'level', 'classRoom', 'prof'])
            ->active()
            ->whereHas('subjectModel', function ($subjectQuery) {
                $subjectQuery->where('status', 'active');
            })
            ->where(function ($outer) use ($paths) {
                foreach ($paths as $path) {
                    if (!$path->subject_id || !$path->class_id) {
                        continue;
                    }

                    $outer->orWhere(function ($scheduleQuery) use ($path) {
                        $scheduleQuery
                            ->where('subject_id', $path->subject_id)
                            ->where('class_id', $path->class_id);

                        if ($path->level_id) {
                            $scheduleQuery->where('level_id', $path->level_id);
                        }

                        if (trim((string) $path->slot_code) !== '') {
                            $slot = strtoupper(trim((string) $path->slot_code));
                            $scheduleQuery->where(function ($slotQuery) use ($slot) {
                                $slotQuery->whereNull('slot_code')
                                    ->orWhereRaw('UPPER(TRIM(slot_code)) = ?', [$slot]);
                            });
                        }
                    });
                }
            });

        return $query->orderBy('day_of_week')->orderBy('start_time')
            ->get()->unique('id')->values();
    }

    private function resultsCount(int $studentId): int
    {
        return Result::where('user_id', $studentId)->count()
            + VocalTestSubmission::where('user_id', $studentId)
                ->where(function ($query) {
                    $query->whereNotNull('final_score')->orWhereNotNull('score');
                })->count()
            + HighSchoolTestSubmission::where('user_id', $studentId)
                ->whereNotNull('score')->count();
    }
}
