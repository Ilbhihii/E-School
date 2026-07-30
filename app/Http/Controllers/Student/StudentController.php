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
use App\Models\Result;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\LearningPathService;
use App\Models\HighSchoolTestSubmission;
use App\Models\VocalTestPrompt;

class StudentController extends Controller
{
    private LearningPathService $paths;

    public function __construct(LearningPathService $paths)
    {
        $this->paths = $paths;
    }

    // dashboard étudiant
    public function dashboard()
    {
        $user = auth()->user();

        // Seuls les étudiants inactifs sont redirigés
        if ($user->role === 'student' && !$user->is_active) {
            return redirect()->route('student.waiting');
        }
        $assignmentRows = $this->paths->studentAssignmentRows($user->id);
        $classIds = $assignmentRows->pluck('class_id')->unique()->values();
        $subjectIds = $assignmentRows->pluck('subject_id')->unique()->values();

        $classRoom = $classIds->isNotEmpty()
            ? ClassRoom::with('level')->find($classIds->first())
            : null;
        $assignedSubject = $subjectIds->isNotEmpty()
            ? Subject::find($subjectIds->first())
            : null;
        $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();

        $courseQuery = Course::query();
        if ($assignmentRows->isEmpty()) {
            $courseQuery->whereRaw('1 = 0');
        } else {
            $courseQuery->where(function ($query) use ($assignmentRows) {
                foreach ($assignmentRows as $row) {
                    $query->orWhere(function ($pair) use ($row) {
                        $pair->where('subject_id', $row->subject_id)
                            ->where('level_id', $row->level_id)
                            ->where('class_id', $row->class_id);
                    });
                }
            });
        }
        $coursesCount = (clone $courseQuery)->count();
        $recentCourses = (clone $courseQuery)->latest()->take(4)->get();
        $recentCourses2 = (clone $courseQuery)->latest()->take(2)->get();
        $livesCount = $classIds->isNotEmpty()
            ? Live::whereIn('class_id', $classIds)->count()
            : 0;

        $profAssignments = Assignment::query()
            ->when($classRoom, fn($query) => $query->where('class_room_id', $classRoom->id))
            ->when($assignedSubject, fn($query) => $query->where('subject_id', $assignedSubject->id))
            ->whereHas('user', fn($query) => $query->where('role', 'prof'));
        $totalAssignments = $classRoom && $assignedSubject ? $profAssignments->count() : 0;

        $assignmentsQuery = Assignment::where('user_id', $user->id)
            ->when($assignedSubject, fn($query) => $query->where('subject_id', $assignedSubject->id));
        $assignments = (clone $assignmentsQuery)->latest()->get();
        $assignmentsSent = $assignments->count();
        $assignmentsCorrected = $assignments->whereNotNull('grade')->count();
        $assignmentCompletion = $totalAssignments > 0
            ? min(100, round(($assignmentsSent / $totalAssignments) * 100))
            : ($assignmentsSent > 0 ? 100 : 0);
        $sentPercent = $assignmentCompletion;
        $correctedPercent = $assignmentsSent > 0
            ? round(($assignmentsCorrected / $assignmentsSent) * 100)
            : 0;

        $attendanceRecords = Absence::where('user_id', $user->id)->get();
        $totalSessions = $attendanceRecords->count();
        $totalAbsences = $attendanceRecords->where('present', false)->count();
        $presencePercent = $totalSessions > 0
            ? round(($attendanceRecords->where('present', true)->count() / $totalSessions) * 100)
            : 100;

        $average = $assignments->whereNotNull('grade')->avg('grade') ?? 0;
        $grades = $assignments->whereNotNull('grade')->pluck('grade')->values();
        $engagement = round(($assignmentCompletion + $correctedPercent + $presencePercent) / 3);

        $recentAssignments = $assignments->take(2);
        $absences = $attendanceRecords->where('present', false)->sortByDesc('date')->take(3)->values();
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
            'assignmentCompletion',
            'totalAssignments',
            'average',
            'grades',
            'assignments',
            'sentPercent',
            'correctedPercent',
            'presencePercent',
            'engagement',
            'subjects',
            'classRoom',
            'assignedSubject',
            'recentCourses2'
        ));

    }


    public function lives()
    {
        $user = auth()->user();

        // 🔒 Classes accessibles : classe principale + classes via class_user (assignations individuelles)
        $classIds = collect([$user->class_id])->filter();

        $assignedClassIds = \DB::table('class_user')
            ->where('user_id', $user->id)
            ->whereNotNull('class_id')
            ->pluck('class_id')
            ->unique();

        $classIds = $classIds->merge($assignedClassIds)->unique()->values();

        $lives = Live::whereIn('class_id', $classIds)->latest()->get();

        return view('student.lives', compact('lives'));
    }


    public function classCourses($id)
    {
        $user = auth()->user();
        $rows = $this->paths->studentAssignmentRows($user->id)
            ->where('class_id', (int) $id);
        abort_if($rows->isEmpty(), 403);

        $class = ClassRoom::with('level')->findOrFail($id);
        $courses = Course::query()
            ->where('class_id', $class->id)
            ->whereIn('subject_id', $rows->pluck('subject_id'))
            ->get();

        return view('student.class.courses', compact('class', 'courses'));
    }


    public function coursesBySubject($classId, $subjectId)
    {
        $class = ClassRoom::with('level')->findOrFail($classId);
        abort_unless(
            $class->level
            && $this->paths->studentCanAccessPath(
                auth()->user(),
                (int) $subjectId,
                (int) $class->level_id,
                (int) $class->id
            ),
            403
        );

        $courses = Course::where('class_id', $class->id)
            ->where('subject_id', $subjectId)
            ->where('level_id', $class->level_id)
            ->get();

        return view('student.class.courses', compact('courses', 'class'));
    }


    public function showCourse($id)
    {
        $course = Course::with([
                'subject',
                'level',
                'classRoom.level',
                'devoirs',
            ])
            ->findOrFail($id);

        abort_unless(
            $this->paths->userCanAccessCourse(
                auth()->user(),
                $course
            ),
            403
        );

        $courseLevel =
            $course->level
            ?? $course->classRoom?->level;

        abort_unless(
            $course->subject
            && $courseLevel
            && $course->classRoom,
            404
        );

        $this->ensureHighSchoolTestApproved(
            $course->subject,
            $courseLevel,
            $course->classRoom
        );

        $resourceUrls = [];

        foreach (
            ['video', 'pdf', 'link']
            as $type
        ) {
            $exists = $type === 'video'
                ? (
                    $course->video
                    || $course->video_url
                )
                : (
                    $type === 'pdf'
                        ? $course->pdf
                        : $course->course_link
                );

            if ($exists) {
                $resourceUrls[$type] =
                    \URL::temporarySignedRoute(
                        'course.resource',
                        now()->addMinutes(10),
                        [
                            'course' =>
                                $course->id,
                            'type' => $type,
                        ]
                    );
            }
        }

        return view(
            'student.class.course-show',
            compact(
                'course',
                'resourceUrls'
            )
        );
    }


    // page devoirs étudiant
    public function assignments()
    {
        $assignments = Assignment::where('user_id',auth()->id())
                    ->orderBy('created_at','desc')
                    ->get();

        $classRoom = auth()->user()->classRoom;
        $courses = $classRoom?->courses ?? collect([]);

        // Matières de la classe + matières assignées individuellement
        $subjects = $classRoom?->subjects ?? collect([]);
        $subjects = $subjects->merge(auth()->user()->individuallyAssignedSubjects())->unique('id');

        // Devoirs postés par les professeurs pour cette classe
        $profAssignments = collect([]);
        if ($classRoom) {
            $profAssignments = Assignment::where('class_room_id', $classRoom->id)
                ->whereHas('user', function($q) {
                    $q->where('role', 'prof');
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            $now = now();
            // Pour chaque devoir du prof, vérifier si l'étudiant a soumis
            $profAssignments->each(function($pa) use ($now) {
                $dueDate = $pa->due_date ? \Carbon\Carbon::parse($pa->due_date) : null;
                $isOverdue = $dueDate && $now->gt($dueDate);

                $studentSubmission = Assignment::where('user_id', auth()->id())
                    ->where(function($q) use ($pa) {
                        $q->where('course_id', $pa->course_id);
                        if ($pa->title) {
                            $q->orWhere('title', 'like', '%' . $pa->title . '%');
                        }
                    })
                    ->first();

                // Vérifier si le devoir a assez d'informations pour être soumis
                $pa->has_file = !empty($pa->file);
                $pa->is_locked = $isOverdue || !$pa->has_file;

                if ($studentSubmission) {
                    $pa->student_submitted = true;
                    if ($studentSubmission->grade !== null) {
                        $pa->student_grade = $studentSubmission->grade;
                        $pa->student_grade_status = $studentSubmission->grade >= 10 ? 'acqui' : 'non_acquis';
                    } else {
                        $pa->student_grade_status = 'en_cours';
                    }
                } else {
                    $pa->student_submitted = false;
                    $pa->student_grade_status = 'non_acquis';
                }
            });
        }

        $hasSingleSubject = $subjects->count() === 1;

        return view('student.assignments', compact(
            'assignments', 'courses', 'classRoom', 'subjects',
            'profAssignments', 'hasSingleSubject'
        ));
    }


    // envoyer devoir
    public function sendAssignment(Request $request)
    {
        $user = auth()->user();
        $userClassRoom = $user->classRoom;

        // Matières assignées à l'étudiant
        $validSubjects = collect();
        if ($userClassRoom) {
            $validSubjects = $userClassRoom->subjects;
        }
        $validSubjects = $validSubjects->merge($user->individuallyAssignedSubjects())->unique('id');

        if ($validSubjects->count() === 0) {
            return back()->with('error', 'Aucune matière assignée à votre compte. Veuillez contacter l\'administration.');
        }

        // Déterminer la matière
        $subjectId = $request->subject_id;

        if (!$subjectId) {
            // Auto-détection si une seule matière
            if ($validSubjects->count() === 1) {
                $subjectId = $validSubjects->first()->id;
            } else {
                return back()->with('error', 'Veuillez sélectionner une matière dans le formulaire.');
            }
        } else {
            // Vérifier que la matière fournie appartient bien à l'étudiant
            if (!$validSubjects->pluck('id')->contains((int)$subjectId)) {
                return back()->with('error', 'Cette matière ne vous est pas assignée.');
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB
        ]);

        // Trouver un cours dans cette matière pour la classe de l'étudiant
        $course = Course::where('subject_id', $subjectId)
            ->where('class_id', $userClassRoom?->id)
            ->first();

        if (!$course) {
            return back()->with('error', 'Aucun cours disponible pour cette matière dans votre classe.');
        }

        $file = $request->file('file')->store('assignments','public');

        Assignment::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'file' => $file,
            'course_id' => $course->id,
            'subject_id' => $subjectId,
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

        // La colonne 'justified' n'existe pas dans la table
        $justifiedCount = 0;

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
            'justifiedCount',
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
        $rows = $this->paths->studentAssignmentRows($user->id);
        $subjects = Subject::whereIn('id', $rows->pluck('subject_id'))->orderBy('name')->get();
        $firstRow = $rows->first();
        $classRoom = $firstRow ? ClassRoom::with('level')->find($firstRow->class_id) : null;
        $level = $classRoom ? $classRoom->level : null;

        return view('student.subjects.index', compact('subjects', 'level', 'classRoom'));
    }

// ═══ Navigation hiérarchique : Matières → Niveaux → Classes → Cours ═══

public function subjectLevels(Subject $subject)
{
    $hierarchy = collect($this->paths->hierarchyForStudent(auth()->id()));
    $node = $hierarchy->firstWhere('id', $subject->id);
    abort_unless($node, 403);

    $levels = Level::whereIn('id', collect($node['levels'])->pluck('id'))->orderBy('name')->get();
    return view('student.subjects.levels', compact('subject', 'levels'));
}

public function subjectClasses(Subject $subject, Level $level)
{
    $rows = $this->paths->studentAssignmentRows(auth()->id())
        ->where('subject_id', $subject->id)
        ->where('level_id', $level->id);
    abort_if($rows->isEmpty(), 403);

    $classes = ClassRoom::whereIn('id', $rows->pluck('class_id'))->orderBy('name')->get();
    return view('student.subjects.classes', compact('subject', 'level', 'classes'));
}

public function subjectCourses(
    Subject $subject,
    Level $level,
    ClassRoom $class
) {
    abort_unless(
        $this->paths->studentCanAccessPath(
            auth()->user(),
            $subject->id,
            $level->id,
            $class->id
        ),
        403
    );

    $this->ensureHighSchoolTestApproved(
        $subject,
        $level,
        $class
    );

    $courses = Course::where(
            'subject_id',
            $subject->id
        )
        ->where(
            'level_id',
            $level->id
        )
        ->where(
            'class_id',
            $class->id
        )
        ->withCount('devoirs')
        ->get();

    return view(
        'student.subjects.courses',
        compact(
            'subject',
            'level',
            'class',
            'courses'
        )
    );
}

    public function waiting()
    {
        $user = auth()->user();
        
        // Get the latest test result for this user
        $latestResult = Result::where('user_id', $user->id)
            ->with('test')
            ->latest()
            ->first();
        
        $score = $latestResult?->score ?? 0;
        $total = $latestResult?->total_questions ?? 0;
        $percentage = $latestResult?->percentage ?? 0;
        $testTitle = $latestResult?->test?->title ?? null;
        
        return view('student.waiting', compact(
            'latestResult', 'score', 'total', 'percentage', 'testTitle'
        ));
    }

public function levels()
{
    $user = auth()->user();
    
    // Si l'étudiant a déjà une classe assignée, rediriger vers les matières
    if ($user->classRoom && $user->classRoom->level) {
        return redirect()->route('student.subjects.index')
            ->with('success', 'Vous êtes déjà assigné à la classe ' . $user->classRoom->name . '. Choisissez une matière.');
    }
    
    // Sans classe assignée → ne montre pas tous les niveaux, redirige vers dashboard
    return redirect()->route('student.dashboard')
        ->with('warning', 'Vous n\'avez pas encore de classe assignée. Veuillez contacter l\'administration.');
}

public function levelClasses(Level $level)
{
    return redirect()->route('student.subjects.index')
        ->with('info', 'Utilisez la navigation par matières pour accéder à vos cours.');
}

public function levelSubjects(Level $level, ClassRoom $class)
{
    return redirect()->route('student.subjects.index')
        ->with('info', 'Utilisez la navigation par matières pour accéder à vos cours.');
}

public function subjects(Level $level)
{
    return redirect()->route('student.subjects.index')
        ->with('info', 'Utilisez la navigation par matières pour accéder à vos cours.');
}

public function classes(Subject $subject, Level $level)
{
    return redirect()->route('student.subjects.classes', [$subject, $level]);
}

public function courses(Subject $subject, ClassRoom $class)
{
    $level = $class->level;
    
    if (!$level) {
        return redirect()->route('student.subjects.index')
            ->with('error', 'Niveau non trouvé pour cette classe.');
    }

    abort_unless(
        $this->paths->studentCanAccessPath(auth()->user(), $subject->id, $level->id, $class->id),
        403
    );

    return redirect()->route('student.subjects.courses', [$subject, $level, $class]);
}

    private function ensureHighSchoolTestApproved(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ): void {
        $isHighSchoolSupport =
            VocalTestPrompt::normalizePathName(
                $subject->name
            ) === 'soutien lycee';

        if (!$isHighSchoolSupport) {
            return;
        }

        $approved =
            HighSchoolTestSubmission::query()
                ->where(
                    'user_id',
                    auth()->id()
                )
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
                    $class->id
                )
                ->where(
                    'status',
                    HighSchoolTestSubmission
                        ::STATUS_APPROVED
                )
                ->exists();

        abort_unless(
            $approved,
            403,
            'Votre test écrit doit être validé '
            . 'avant l’accès aux cours.'
        );
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
