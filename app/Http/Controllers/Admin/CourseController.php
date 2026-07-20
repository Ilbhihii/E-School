<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;
use App\Models\ProfAssignment;

class CourseController extends Controller
{
    // ================= SHOW =================
    public function show($id)
    {
        $course = Course::with(['classRoom', 'subject', 'devoirs'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $course->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('admin.courses.show', compact('course'));
    }

    // ================= LISTE =================
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $courses = Course::with(['classRoom', 'subject', 'level', 'assignments'])->paginate(10);
        } else {
            $courses = Course::where('user_id', auth()->id())
                ->with(['classRoom', 'subject', 'level', 'assignments'])
                ->paginate(10);
        }
        return view('admin.courses.index', compact('courses'));
    }

    // ================= CREATE =================
    public function create(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            $levels = Level::with('classes')->get();
            $classes = ClassRoom::with('level')->get();
            $subjects = Subject::all()->unique('name');
        } else {
            $scope = ProfAssignment::where('prof_id', auth()->id())->get();
            $levels = Level::whereIn('id', $scope->pluck('level_id'))->with(['classes' => fn ($query) => $query->whereIn('id', $scope->pluck('class_id'))])->get();
            $classes = ClassRoom::whereIn('id', $scope->pluck('class_id'))->with('level')->get();
            $subjects = Subject::whereIn('id', $scope->pluck('subject_id'))->get();
        }
        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');
        return view('admin.courses.create', compact('levels', 'classes', 'subjects', 'selectedClassId', 'selectedSubjectId'));
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'title' => 'required',
            'description' => 'nullable|string',
            'class_id' => 'required',
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

        // Récupérer le niveau depuis la classe
        $classRoom = ClassRoom::with('level')->findOrFail($request->class_id);
        if (! auth()->user()->isAdmin()) {
            abort_unless(ProfAssignment::where('prof_id', auth()->id())
                ->where('class_id', $classRoom->id)
                ->where('level_id', $classRoom->level_id)
                ->where('subject_id', $request->subject_id)->exists(), 403);
        }

        Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'class_id' => $request->class_id,
            'level_id' => $classRoom->level->id,
            'subject_id' => $request->subject_id,
            'video' => $videoPath ?? null,
            'pdf' => $pdfPath ?? null,
            'course_link' => $request->course_link,
            'admin_id' => auth()->id(),
            'user_id' => auth()->id(),
        ]);

        // 2. Lier automatiquement subject <-> class
        $subject = Subject::find($request->subject_id);
        $subject->classes()->syncWithoutDetaching([
            $request->class_id
        ]);

        return redirect()->back()->with('success', 'Cours créé avec succès');
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $this->authorizeCourseOwner($course);
        if (auth()->user()->isAdmin()) {
            $levels = Level::with('classes')->get();
            $classes = ClassRoom::with('level')->get();
            $subjects = Subject::all()->unique('name');
        } else {
            $scope = ProfAssignment::where('prof_id', auth()->id())->get();
            $levels = Level::whereIn('id', $scope->pluck('level_id'))->with('classes')->get();
            $classes = ClassRoom::whereIn('id', $scope->pluck('class_id'))->with('level')->get();
            $subjects = Subject::whereIn('id', $scope->pluck('subject_id'))->get();
        }

        return view('admin.courses.edit', compact('course', 'levels', 'classes', 'subjects'));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $this->authorizeCourseOwner($course);

        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'class_id' => 'required',
            'subject_id' => 'required|exists:subjects,id',
            'course_link' => 'nullable|url',
            'video' => 'nullable|file|mimes:mp4,mov,avi',
            'pdf' => 'nullable|file|mimes:pdf'
        ]);

        // Récupérer le niveau depuis la classe
        $classRoom = ClassRoom::with('level')->findOrFail($request->class_id);
        if (! auth()->user()->isAdmin()) {
            abort_unless(ProfAssignment::where('prof_id', auth()->id())
                ->where('class_id', $classRoom->id)->where('level_id', $classRoom->level_id)
                ->where('subject_id', $request->subject_id)->exists(), 403);
        }
        $data['level_id'] = $classRoom->level->id;

        if($request->hasFile('video')){
            $data['video'] = $request->file('video')->store('videos','public');
        }

        if($request->hasFile('pdf')){
            $data['pdf'] = $request->file('pdf')->store('pdfs','public');
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')
            ->with('success','Cours mis à jour avec succès');
    }


    // ================= AJAX : matières par classe =================
    public function getClassSubjects($classId)
    {
        $subjects = Subject::whereHas('classes', function($q) use ($classId) {
            $q->where('class_room_id', $classId);
        })->orderBy('name')->get(['id', 'name']);

        return response()->json($subjects->values());
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $this->authorizeCourseOwner($course);

        // Supprimer fichiers
        if ($course->video) {
            Storage::disk('public')->delete($course->video);
        }

        if ($course->pdf) {
            Storage::disk('public')->delete($course->pdf);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Cours supprimé avec succès');
    }

    private function authorizeCourseOwner(Course $course): void
    {
        if (! auth()->user()->isAdmin()) {
            abort_unless((int) $course->user_id === (int) auth()->id(), 403, 'Accès non autorisé');
        }
    }
}
