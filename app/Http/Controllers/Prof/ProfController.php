<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Absence;
use App\Models\ClassRoom;
use App\Models\Classe;
use App\Models\Level;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;
use App\Models\Test;



class ProfController extends Controller
{


    public function dashboard()
    {
        $classesCount = \App\Models\ClassRoom::count();
        $studentsCount = \App\Models\User::where('role', 'student')->count();
        $assignmentsCount = \App\Models\Assignment::count();
        $absencesCount = \App\Models\Absence::where('present', 0)->count();
        $testsCount = Test::where('create_by', auth()->id())->count();

        return view('prof.dashboard', compact('classesCount', 'studentsCount', 'assignmentsCount', 'absencesCount', 'testsCount'));
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
        $assignment->grade = ($status === 'bien') ? 20 : 0;
        $assignment->comment = $request->comment ?? '';

        $assignment->save();

        return back()->with('success','Devoir corrigé avec succès!');
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

    public function index()
    {
        return $this->courses();
    }

    public function livesIndex()
    {
        $lives = \App\Models\Live::where('user_id', auth()->id())
            ->with('classRoom')
            ->latest()
            ->paginate(15);
        
        $totalLives = \App\Models\Live::where('user_id', auth()->id())->count();
        $recentLives = \App\Models\Live::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();
        
        return view('prof.lives.index', compact('lives', 'totalLives', 'recentLives'));
    }

    public function courses()
    {
        $courses = Course::where('user_id', auth()->id())
            ->with(['level', 'subject'])
            ->latest()
            ->paginate(15);
        return view('prof.courses.index', compact('courses'));
    }

    public function create()
    {
        $levels = Level::all();
        $subjects = Subject::with('classRoom')->get();
        return view('prof.courses.create', compact('levels', 'subjects'));
    }

    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'title' => 'required',
            'description' => 'nullable|string',
            'level_id' => 'required',
            'subject_id' => 'required|exists:subjects,id',
            'course_link' => 'nullable|url',
            'video' => 'nullable|mimes:mp4,mov,avi|max:204800',
            'pdf' => 'nullable|mimes:pdf|max:20480',
        ]);

        $videoPath = null;
        $pdfPath = null;

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
        }

        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('pdfs', 'public');
        }

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'level_id' => $request->level_id,
            'subject_id' => $request->subject_id,
            'video' => $videoPath,
            'pdf' => $pdfPath,
            'course_link' => $request->course_link,
            'user_id' => auth()->id(),
            'admin_id' => auth()->id(),
        ]);

        // 🔥 4. SI devoir rempli → créer devoir
        if($request->assignment_title){

            $filePath = null;
            if($request->hasFile('assignment_file')){
                $filePath = $request->file('assignment_file')->store('assignments','public');
            }

            Assignment::create([
                'title' => $request->assignment_title,
                'description' => $request->assignment_description,
                'due_date' => $request->assignment_due_date,
                'file' => $filePath ?? null,
                'course_id' => $course->id,
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('prof.courses.index')
            ->with('success','Cours + devoir créés avec succès');
    }

    public function edit(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        $levels = Level::all();
        $subjects = Subject::with('classRoom')->get();
        return view('prof.courses.edit', compact('course', 'levels', 'subjects'));
    }

    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'level_id' => 'required',
            'subject_id' => 'required|exists:subjects,id',
            'course_link' => 'nullable|url',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:204800',
            'pdf' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $data = $request->only(['title', 'description', 'level_id', 'subject_id', 'course_link']);

        if ($request->hasFile('video')) {
            if ($course->video) {
                Storage::disk('public')->delete($course->video);
            }
            $data['video'] = $request->file('video')->store('videos', 'public');
        }

        if ($request->hasFile('pdf')) {
            if ($course->pdf) {
                Storage::disk('public')->delete($course->pdf);
            }
            $data['pdf'] = $request->file('pdf')->store('pdfs', 'public');
        }

        $course->update($data);

        return redirect()->route('prof.courses.index')->with('success', 'Cours mis à jour');
    }

    public function destroy(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        if ($course->video) {
            Storage::disk('public')->delete($course->video);
        }

        if ($course->pdf) {
            Storage::disk('public')->delete($course->pdf);
        }

        $course->delete();

        return back()->with('success', 'Cours supprimé avec succès');
    }
}
