<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Absence;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Course;
use App\Models\Live;
use App\Models\ProfAssignment;
use Illuminate\Support\Facades\DB;


class ProfController extends Controller
{


    public function dashboard()
    {
        $profAssignments = ProfAssignment::with(['subject', 'level', 'classRoom'])
            ->where('prof_id', auth()->id())
            ->get();
        $classIds = $profAssignments->pluck('class_id')->unique()->values();
        $subjectIds = $profAssignments->pluck('subject_id')->unique()->values();

        $studentIds = $profAssignments->isEmpty() ? collect() : DB::table('class_user')
            ->join('users', 'users.id', '=', 'class_user.user_id')
            ->where('users.role', 'student')
            ->where(function ($query) use ($profAssignments) {
                foreach ($profAssignments as $assignment) {
                    $query->orWhere(function ($pair) use ($assignment) {
                        $pair->where('class_user.class_id', $assignment->class_id)
                            ->where('class_user.subject_id', $assignment->subject_id);
                    });
                }
            })
            ->distinct()
            ->pluck('users.id');

        $coursesQuery = Course::where('user_id', auth()->id());
        $devoirsQuery = Assignment::where('user_id', auth()->id());
        $submissionsQuery = Assignment::whereIn('user_id', $studentIds)
            ->whereIn('subject_id', $subjectIds);
        $attendanceQuery = Absence::whereIn('user_id', $studentIds);

        $studentsCount = $studentIds->count();
        $coursesCount = (clone $coursesQuery)->count();
        $myDevoirsCount = (clone $devoirsQuery)->count();
        $assignmentsCount = (clone $submissionsQuery)->count();
        $correctedCount = (clone $submissionsQuery)->whereNotNull('grade')->count();
        $pendingCount = max($assignmentsCount - $correctedCount, 0);
        $correctionRate = $assignmentsCount > 0 ? round(($correctedCount / $assignmentsCount) * 100) : 0;
        $averageGrade = (float) ((clone $submissionsQuery)->whereNotNull('grade')->avg('grade') ?? 0);
        $absencesCount = (clone $attendanceQuery)->where('present', false)->count();
        $attendanceCount = (clone $attendanceQuery)->count();
        $presenceRate = $attendanceCount > 0
            ? round(((clone $attendanceQuery)->where('present', true)->count() / $attendanceCount) * 100)
            : 100;
        $livesCount = Live::where('user_id', auth()->id())->count();
        $recentSubmissions = (clone $submissionsQuery)->with(['user', 'subject'])->latest()->take(5)->get();

        return view('prof.dashboard', compact(
            'studentsCount', 'coursesCount', 'assignmentsCount', 'myDevoirsCount',
            'correctedCount', 'pendingCount', 'correctionRate', 'averageGrade',
            'absencesCount', 'presenceRate', 'livesCount', 'profAssignments',
            'recentSubmissions'
        ));
    }

    public function assignments()
    {
        $scope = ProfAssignment::where('prof_id', auth()->id())->get();
        $studentIds = $scope->isEmpty() ? collect() : DB::table('class_user')
            ->where(function ($query) use ($scope) {
                foreach ($scope as $item) {
                    $query->orWhere(fn ($pair) => $pair
                        ->where('class_id', $item->class_id)
                        ->where('subject_id', $item->subject_id));
                }
            })->pluck('user_id')->unique();
        $assignments = Assignment::with(['user', 'subject'])
            ->whereIn('user_id', $studentIds)
            ->whereIn('subject_id', $scope->pluck('subject_id'))
            ->latest()->get();
        return view('prof.assignments', compact('assignments'));
    }

    public function grade(Request $request)
    {
        $assignment = Assignment::whereKey($request->id)
            ->whereHas('user', fn ($query) => $query->where('role', 'student'))
            ->findOrFail($request->id);
        $allowed = ProfAssignment::where('prof_id', auth()->id())
            ->where('subject_id', $assignment->subject_id)
            ->exists();
        abort_unless($allowed, 403);

        $status = $request->status;
        $assignment->grade = match($status) {
            'acquis' => 20,
            'en_cours' => 10,
            'non_acquis' => 0,
            default => 0,
        };
        $assignment->comment = $request->comment ?? '';

        $assignment->save();

        $label = match($assignment->grade) {
            20 => 'Acquis',
            10 => 'En cours d\'acquisition',
            default => 'Non acquis',
        };

        return back()->with('success', "Devoir corrigé : {$label}");
    }

    public function absences()
    {

        $classes = $this->assignedClasses();

        return view('prof.absences',compact('classes'));

    }

    public function absencesList(Request $request)
    {
        $studentIds = $this->assignedStudentIds();
        $query = Absence::with(['user.classRoom'])->whereIn('user_id', $studentIds)->latest('created_at');

        // Filter by class
        if ($request->class_id) {
            $query->whereHas('user.classRoom', function($q) use ($request) {
                $q->where('id', $request->class_id);
            });
        }

        // Sorting
        if ($request->has('sort')) {
            $dir = $request->dir ?? 'desc';
            $query->orderBy($request->sort === 'user.name' ? 'user_id' : $request->sort, $dir);
        }

        $absences = $query->paginate(15);
        $classes = $this->assignedClasses();

        return view('prof.absences-list', compact('absences', 'classes'));
    }


    public function updateAbsence(Request $request, $id)
    {
        $request->validate([
            'present' => 'required|boolean'
        ]);

        $absence = Absence::whereIn('user_id', $this->assignedStudentIds())->findOrFail($id);
        $absence->present = (int) $request->present;
        $absence->save();

        $status = $absence->present ? 'Présent' : 'Absent';
        return back()->with('success', "Absence mise à jour: {$status}");
    }

    public function getStudents($id)
    {
        abort_unless($this->assignedClasses()->contains('id', (int) $id), 403);
        $classRoom = ClassRoom::findOrFail($id);

        $students = $classRoom->users()->where('role', 'student')->select('id', 'name')->get();

        return response()->json($students);
    }



    public function storeAbsence(Request $request)
    {
        $request->validate(['students' => ['required', 'array']]);
        $allowedStudentIds = $this->assignedStudentIds();
        $alertStudents = [];

        foreach($request->students as $studentId => $status){
            abort_unless($allowedStudentIds->contains((int) $studentId), 403);

            Absence::create([
            'user_id'=>$studentId,
            'date'=>now(),
            'present'=>$status == '1' ? 1 : 0
            ]);

            // Count previous absences today or before
            $prevAbsences = Absence::where('user_id',$studentId)
            ->where('present',0)
            ->whereDate('date', '<=', now())
            ->count();

            if($prevAbsences >= 3){
                $alertStudents[$studentId] = $studentId;
            }

        }

        if(!empty($alertStudents)){
            session()->flash('alert', '⚠️ Ces étudiants ont dépassé 3 absences');
        }

    return back()->with('success','Absences enregistrées');

    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->update($request->only('name', 'email'));
        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);
        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Mot de passe mis à jour avec succès !');
    }

    public function browseLives(Level $level, ClassRoom $class)
    {
        $lives = Live::where('class_id', $class->id)
            ->latest()
            ->get();
        return view('prof.lives.browse', compact('level', 'class', 'lives'));
    }

    public function browseDevoirs(Level $level, ClassRoom $class, Subject $subject)
    {
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->where('user_id', auth()->id())
            ->with('devoirs')
            ->get();
        return view('prof.devoir.browse', compact('level', 'class', 'subject', 'courses'));
    }

    public function livesIndex()
    {
        $lives = Live::with('classRoom')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
        
        $totalLives = Live::where('user_id', auth()->id())->count();
        $recentLives = Live::where('user_id', auth()->id())->latest()
            ->limit(5)
            ->get();
        $upcomingLives = Live::where('user_id', auth()->id())
            ->where(fn ($query) => $query->where('live_date', '>=', now())->orWhereNull('live_date'))
            ->count();
        
        return view('prof.lives.index', compact('lives', 'totalLives', 'recentLives', 'upcomingLives'));
    }

    // ═══ Navigation hiérarchique : Matières → Niveaux → Classes → Cours ═══

    public function subjectsList()
    {
        $scope = ProfAssignment::where('prof_id', auth()->id())->get();
        $subjects = Subject::whereIn('id', $scope->pluck('subject_id'))->orderBy('name')->get();
        $subjects->each(function ($subject) use ($scope) {
            $subjectScope = $scope->where('subject_id', $subject->id);
            $subject->assigned_levels_count = $subjectScope->pluck('level_id')->unique()->count();
            $subject->assigned_classes_count = $subjectScope->pluck('class_id')->unique()->count();
        });
        return view('prof.subjects.index', compact('subjects'));
    }

    public function subjectLevels(Subject $subject)
    {
        $levelIds = ProfAssignment::where('prof_id', auth()->id())
            ->where('subject_id', $subject->id)->pluck('level_id')->unique();
        abort_if($levelIds->isEmpty(), 403);
        $levels = Level::whereIn('id', $levelIds)->orderBy('name')->get();
        return view('prof.subjects.levels', compact('subject', 'levels'));
    }

    public function subjectClasses(Subject $subject, Level $level)
    {
        $classIds = ProfAssignment::where('prof_id', auth()->id())
            ->where('subject_id', $subject->id)->where('level_id', $level->id)->pluck('class_id');
        abort_if($classIds->isEmpty(), 403);
        $classes = ClassRoom::whereIn('id', $classIds)
            ->get();
        return view('prof.subjects.classes', compact('subject', 'level', 'classes'));
    }

    /**
     * Affiche les cours d'une matière pour une classe dans le parcours Matière → Niveau → Classe → Cours
     */
    public function subjectCourses(Subject $subject, Level $level, ClassRoom $class)
    {
        $this->authorizeTeachingScope($subject, $level, $class);
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->where('user_id', auth()->id())
            ->with(['classRoom', 'subject'])
            ->get();

        return view('prof.subjects.courses', compact('subject', 'level', 'class', 'courses'));
    }

    /**
     * Affiche les lives d'une classe dans le parcours Matière → Niveau → Classe → Lives
     */
    public function subjectLives(Subject $subject, Level $level, ClassRoom $class)
    {
        $this->authorizeTeachingScope($subject, $level, $class);
        $lives = Live::where('class_id', $class->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('prof.subjects.lives', compact('subject', 'level', 'class', 'lives'));
    }

    /**
     * Affiche les devoirs d'une matière pour une classe dans le parcours Matière → Niveau → Classe → Devoirs
     */
    public function subjectDevoirs(Subject $subject, Level $level, ClassRoom $class)
    {
        $this->authorizeTeachingScope($subject, $level, $class);
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->where('user_id', auth()->id())
            ->with('devoirs')
            ->get();

        return view('prof.subjects.devoirs', compact('subject', 'level', 'class', 'courses'));
    }

    private function assignedClasses()
    {
        $ids = ProfAssignment::where('prof_id', auth()->id())->pluck('class_id')->unique();
        return ClassRoom::whereIn('id', $ids)->orderBy('name')->get();
    }

    private function assignedStudentIds()
    {
        $scope = ProfAssignment::where('prof_id', auth()->id())->get();
        if ($scope->isEmpty()) {
            return collect();
        }
        return DB::table('class_user')
            ->where(function ($query) use ($scope) {
                foreach ($scope as $item) {
                    $query->orWhere(fn ($pair) => $pair
                        ->where('class_id', $item->class_id)
                        ->where('subject_id', $item->subject_id));
                }
            })->pluck('user_id')->unique()->values();
    }

    private function authorizeTeachingScope(Subject $subject, Level $level, ClassRoom $class): void
    {
        abort_unless(ProfAssignment::where('prof_id', auth()->id())
            ->where('subject_id', $subject->id)
            ->where('level_id', $level->id)
            ->where('class_id', $class->id)
            ->exists(), 403);
    }

}
