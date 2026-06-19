<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;

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
        $levels = Level::with('classes')->get();
        $classes = ClassRoom::with('level')->get();
        $subjects = Subject::all()->unique('name');
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
        $levels = Level::with('classes')->get();
        $classes = ClassRoom::with('level')->get();
        $subjects = Subject::all()->unique('name');

        return view('admin.courses.edit', compact('course', 'levels', 'classes', 'subjects'));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

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
}
