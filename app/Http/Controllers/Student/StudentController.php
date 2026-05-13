<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Live;
use App\Models\Absence;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{

    // dashboard étudiant
    public function dashboard()
    {
        $user = auth()->user();

        if (!$user->is_active) {
            return redirect()->route('student.waiting');
        }

        $classRoom = $user->classRoom()->with('subjects')->first();
        $subjects = $classRoom?->subjects ?? collect([]);
        $coursesCount = $classRoom?->courses()->count() ?? 0;
        $livesCount = Live::count();

        $recentCourses = $classRoom?->courses()->latest()->take(4)->get() ?? collect([]);

        /* 🔥 NEW METRICS FROM TASK */
        $totalAssignments = Assignment::count();

        $assignmentsSent = Assignment::where('user_id', auth()->id())->count();

        $assignmentsCorrected = Assignment::where('user_id', auth()->id())
            ->whereNotNull('grade')
            ->count();

        /* 🔥 POURCENTAGES */
        $sentPercent = $totalAssignments > 0 ? ($assignmentsSent / $totalAssignments) * 100 : 0;

        $correctedPercent = $assignmentsSent > 0 ? ($assignmentsCorrected / $assignmentsSent) * 100 : 0;

        /* ABSENCES */
        $totalSessions = Absence::count();

        $absencesCount = Absence::where('user_id', auth()->id())->count();  // renamed to avoid conflict

        $presencePercent = $totalSessions > 0 ? (100 - (($absencesCount / $totalSessions) * 100)) : 100;

        /* MOYENNE */
        $average = Assignment::where('user_id', auth()->id())
            ->whereNotNull('grade')
            ->avg('grade') ?? 0;

        /* ENGAGEMENT (simple calcul) */
        $engagement = ($sentPercent + $presencePercent) / 2;


        /* récupérer assignments de l'étudiant */
        $assignments = Assignment::where('user_id', auth()->id())->get();

        /* notes pour graphique */
        $grades = $assignments
                    ->whereNotNull('grade')
                    ->pluck('grade')
                    ->values(); // 🔥 important


        /* Activités Récent */
        $recentCourses2 = Course::latest()->take(2)->get();

        $recentAssignments = Assignment::where('user_id', auth()->id())
                            ->latest()
                            ->take(2)
                            ->get();

        $absences = Absence::where('user_id', auth()->id())
            ->where('present', false)
            ->latest()
            ->take(3)
            ->get();

        $totalAbsences = Absence::where('user_id', auth()->id())
                    ->where('present', false)
                    ->count();
        if($totalAbsences <= 2){
            $situation = "Situation normale";
        }
        elseif($totalAbsences <= 4){
            $situation = "Avertissement oral";
        }
        else{
            $situation = "Avertissement écrit (message ou appel parents)";
        }

        return view('student.dashboard', compact(
            'coursesCount',
            'livesCount', 
            'recentCourses',
            'recentAssignments',
            'absences',
            'situation',
            'totalAbsences',
            'assignmentsSent',
            'assignmentsCorrected',
            'average',
            'grades',
            'assignments',
            'sentPercent',
            'correctedPercent',
            'presencePercent',
            'engagement',
            'subjects',
            'classRoom',
            'recentCourses2'
        ));

    }


    public function lives()
    {
        $user = auth()->user();

        $lives = Live::latest()->get();
        return view('student.lives', compact('lives'));
    }


    public function classCourses($id)
    {
        $class = \App\Models\ClassRoom::with('courses')->findOrFail($id);

        $courses = $class->courses;

        return view('student.class.courses', compact('class', 'courses'));
    }


    public function coursesBySubject($classId, $subjectId)
    {
        $courses = \App\Models\Course::where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->get();

        $class = \App\Models\ClassRoom::findOrFail($classId);

        return view('student.class.courses', compact('courses', 'class'));
    }


    public function showCourse($id)
    {
$course = Course::with(['subject', 'classRoom', 'devoirs'])->findOrFail($id);

        return view('student.class.course-show', compact('course'));
    }


    // page devoirs étudiant
    public function assignments()
    {
        $assignments = Assignment::where('user_id',auth()->id())
                    ->orderBy('created_at','desc')
                    ->get();

        $userClassRoom = auth()->user()->classRoom;
        $courses = $userClassRoom?->courses ?? collect([]);

        return view('student.assignments', compact('assignments', 'courses'));

    }


    // envoyer devoir
    public function sendAssignment(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $userClassRoom = auth()->user()->classRoom;
        if ($course->classRoom && $course->classRoom->id !== $userClassRoom?->id) {
            return back()->with('error', 'Ce cours ne vous est pas accessible.');
        }

        $file = $request->file('file')->store('assignments','public');

        Assignment::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'file' => $file,
            'course_id' => $request->course_id,
        ]);

        return back()->with('success','Devoir envoyé avec succès !');
    }




public function profile()
    {
        return view('student.profile');
    }

public function settings()
    {
        return view('student.settings');
    }

    public function absences()
    {
        $absences = Absence::where('user_id', auth()->id())
                    ->where('present', false)
                    ->latest()
                    ->get();

        $totalAbsences = $absences->count();

        // situation étudiant
        if ($totalAbsences <= 2) {
            $situation = "Situation normale";
            $color = "success";
        } 
        elseif ($totalAbsences <= 4) {
            $situation = "Avertissement oral";
            $color = "warning";
        } 
        else {
            $situation = "Avertissement écrit (message ou appel parents)";
            $color = "danger";
        }

        return view('student.absences', compact(
            'absences',
            'totalAbsences',
            'situation',
            'color'
        ));
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

public function indexSubjects()
{
    $user = auth()->user();
    $classRoom = $user->classRoom()->with('level.subjects')->first();
    
    if (!$classRoom || !$classRoom->level) {
        return redirect()->route('student.levels')
            ->with('warning', 'Aucune classe ou niveau assigné. Choisissez un niveau.');
    }
    
    $level = $classRoom->level;
    $subjects = Subject::whereHas('classes', function($q) use ($level) {
        $q->where('level_id', $level->id);
    })->get();
    
    return view('student.subjects', compact('subjects', 'level'));
}

public function levels()
{
    $levels = Level::all();
    return view('student.levels', compact('levels'));
}

public function subjects(Level $level)
{

    $subjects = Subject::whereHas('classes', function($q) use ($level){
        $q->where('level_id', $level->id);
    })->get();

    return view('student.subjects', compact('subjects', 'level'));
}

public function classes(Subject $subject, Level $level)
{

    $classes = $subject->classes()
        ->where('level_id', $level->id)
        ->get();

    return view('student.class.subject-classes', compact('classes', 'subject', 'level'));
}

public function courses(Subject $subject, ClassRoom $class)
{

    $courses = Course::where('subject_id', $subject->id)
        ->where('class_id', $class->id)
        ->withCount('devoirs')
        ->get();

    return view('student.class.courses', compact('courses', 'class', 'subject'));
}

    public function index(Request $request)
    {
        $classId = $request->query('class') ?? $request->query('1') ?? null;
        $subjectId = $request->query('subject') ?? $request->query('2') ?? null;
        
        if ($classId) {
            return redirect()->route('student.class.courses', $classId);
        }

        $user = auth()->user();

        $classes = ClassRoom::where('id', $user->class_id)
                    ->withCount('courses')
                    ->get();

        return view('student.courses', compact('classes'));
    }


}
