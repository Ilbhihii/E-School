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


class ProfController extends Controller
{


    public function dashboard()
    {
        $studentsCount = \App\Models\User::where('role', 'student')->count();
        $assignmentsCount = \App\Models\Assignment::count();
        $myDevoirsCount = \App\Models\Assignment::where('user_id', auth()->id())->count();
        $absencesCount = \App\Models\Absence::where('present', 0)->count();
        $livesCount = \App\Models\Live::count();

        return view('prof.dashboard', compact('studentsCount', 'assignmentsCount', 'myDevoirsCount', 'absencesCount', 'livesCount'));
    }

    public function assignments()
    {
        $assignments = Assignment::with('user')->latest()->get();
        return view('prof.assignments', compact('assignments'));
    }

    public function grade(Request $request)
    {
        $assignment = Assignment::findOrFail($request->id);

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

$classes = ClassRoom::all();

        return view('prof.absences',compact('classes'));

    }

    public function absencesList(Request $request)
    {
        $query = Absence::with(['user.classRoom'])->latest('created_at');

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
        $classes = ClassRoom::all();

        return view('prof.absences-list', compact('absences', 'classes'));
    }


    public function updateAbsence(Request $request, $id)
    {
        $request->validate([
            'present' => 'required|boolean'
        ]);

        $absence = Absence::findOrFail($id);
        $absence->present = (int) $request->present;
        $absence->save();

        $status = $absence->present ? 'Présent' : 'Absent';
        return back()->with('success', "Absence mise à jour: {$status}");
    }

    public function getStudents($id)
    {
$classRoom = ClassRoom::findOrFail($id);

        $students = $classRoom->users()->where('role', 'student')->select('id', 'name')->get();

        return response()->json($students);
    }



    public function storeAbsence(Request $request)
    {
        $alertStudents = [];

        foreach($request->students as $studentId => $status){

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
            ->latest()
            ->paginate(15);
        
        $totalLives = \App\Models\Live::count();
        $recentLives = \App\Models\Live::latest()
            ->limit(5)
            ->get();
        $upcomingLives = \App\Models\Live::where('live_date', '>=', now())
            ->orWhereNull('live_date')
            ->count();
        
        return view('prof.lives.index', compact('lives', 'totalLives', 'recentLives', 'upcomingLives'));
    }

    // ═══ Navigation hiérarchique : Matières → Niveaux → Classes → Cours ═══

    public function subjectsList()
    {
        $subjects = Subject::with('classes:id,level_id')->withCount('classes')->orderBy('name')->get();
        return view('prof.subjects.index', compact('subjects'));
    }

    public function subjectLevels(Subject $subject)
    {
        $levelIds = $subject->classes()->pluck('class_rooms.level_id')->unique()->filter();
        $levels = Level::whereIn('id', $levelIds)->orderBy('name')->get();
        return view('prof.subjects.levels', compact('subject', 'levels'));
    }

    public function subjectClasses(Subject $subject, Level $level)
    {
        $classes = ClassRoom::where('level_id', $level->id)
            ->whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
            ->get();
        return view('prof.subjects.classes', compact('subject', 'level', 'classes'));
    }

    /**
     * Affiche les cours d'une matière pour une classe dans le parcours Matière → Niveau → Classe → Cours
     */
    public function subjectCourses(Subject $subject, Level $level, ClassRoom $class)
    {
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
        $lives = Live::where('class_id', $class->id)
            ->latest()
            ->get();

        return view('prof.subjects.lives', compact('subject', 'level', 'class', 'lives'));
    }

    /**
     * Affiche les devoirs d'une matière pour une classe dans le parcours Matière → Niveau → Classe → Devoirs
     */
    public function subjectDevoirs(Subject $subject, Level $level, ClassRoom $class)
    {
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->where('user_id', auth()->id())
            ->with('devoirs')
            ->get();

        return view('prof.subjects.devoirs', compact('subject', 'level', 'class', 'courses'));
    }

}
