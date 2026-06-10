<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\ClassRoom;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class DevoirController extends Controller
{
    public function index(Request $request)
    {
        $course_id = $request->course_id;
        $course = null;
        $query = Assignment::with(['user', 'course']);
        if ($course_id) {
            $query->where('course_id', $course_id);
            $course = Course::findOrFail($course_id);
        }
        $devoirs = $query->orderBy('created_at', 'desc')->paginate(10);

        $courses = Course::all(); // for filter

        return view('admin.devoirs.index', compact('devoirs', 'course_id', 'course', 'courses'));
    }

    public function create(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'prof'])) {
            abort(403, 'Accès interdit');
        }
        $course_id = $request->course_id;
        $course = $course_id ? Course::findOrFail($course_id) : null;
        $classes = ClassRoom::all();
        $courses = Course::all();
        return view('admin.devoirs.create', compact('course', 'classes', 'courses', 'course_id'));
    }

    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'prof'])) {
            abort(403, 'Accès interdit');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'due_date' => 'required|date|after:now',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignments', 'public');
        }

        Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'file' => $filePath ?? null,
            'due_date' => $request->due_date,
            'course_id' => $request->course_id,
            'class_room_id' => $request->class_room_id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.devoirs.index', ['course_id' => $request->course_id])->with('success', 'Devoir créé avec succès!');
    }

    public function edit(Assignment $devoir)
    {
        $classes = ClassRoom::all();
        return view('admin.devoirs.edit', compact('devoir', 'classes'));
    }

    public function update(Request $request, Assignment $devoir)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_room_id' => 'required|exists:class_rooms,id',
            'due_date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('file')) {
            if ($devoir->file) {
                Storage::disk('public')->delete($devoir->file);
            }
            $devoir->file = $request->file('file')->store('assignments', 'public');
        }

        $devoir->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'class_room_id' => $request->class_room_id,
        ]);

        return redirect()->route('admin.devoirs.index', ['course_id' => $devoir->course_id])->with('success', 'Devoir mis à jour!');
    }

    public function destroy(Assignment $devoir)
    {
        if ($devoir->file) {
            Storage::disk('public')->delete($devoir->file);
        }

        $course_id = $devoir->course_id;
        $devoir->delete();

        return redirect()->route('admin.devoirs.index', ['course_id' => $course_id])->with('success', 'Devoir supprimé!');
    }
}
?>
</xai:function_call > 

<xai:function_call name="create_file">
<parameter name="absolute_path >
