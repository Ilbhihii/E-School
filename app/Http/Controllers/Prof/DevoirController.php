<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DevoirController extends Controller
{
    public function index(Request $request)
    {
        $course_id = $request->course_id;
        $course = null;
        $query = Assignment::where('user_id', auth()->id());
        if ($course_id) {
            $query->where('course_id', $course_id);
            $course = Course::findOrFail($course_id);
        }
        $devoirs = $query->orderBy('created_at', 'desc')->paginate(10);

        $courses = Course::all(); // for filter

        return view('prof.devoir.index', compact('devoirs', 'course_id', 'course', 'courses'));
    }


    public function create(Request $request)
    {
        $course_id = $request->course_id;
        $course = $course_id ? Course::findOrFail($course_id) : null;
        $classes = ClassRoom::all();
        $courses = Course::all();
        return view('prof.devoir.create', compact('course', 'classes', 'courses', 'course_id'));
    }

    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_room_id' => 'required|exists:class_rooms,id',
            'due_date' => 'required|date|after:now',
            'file' => 'nullable|file|mimes:pdf|max:5120', // 5MB PDF
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

        $redirectRoute = $request->course_id ? route('prof.devoir.index', ['course_id' => $request->course_id]) : route('prof.devoir.index');
        return redirect($redirectRoute)->with('success', 'Devoir créé avec succès!');
    }

    public function edit(Assignment $devoir)
    {
        if ($devoir->user_id !== auth()->id()) {
            abort(403);
        }

        $classes = ClassRoom::all();
        return view('prof.devoir.edit', compact('devoir', 'classes'));
    }

    public function update(Request $request, Assignment $devoir)
    {
        if ($devoir->user_id !== auth()->id()) {
            abort(403);
        }

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

        $redirectRoute = $devoir->course_id ? route('prof.devoir.index', ['course_id' => $devoir->course_id]) : route('prof.devoir.index');
        return redirect($redirectRoute)->with('success', 'Devoir mis à jour!');
    }

    public function destroy(Assignment $devoir)
    {
        if ($devoir->user_id !== auth()->id()) {
            abort(403);
        }

        if ($devoir->file) {
            Storage::disk('public')->delete($devoir->file);
        }

        $course_id = $devoir->course_id;
        $devoir->delete();

        $redirectRoute = $course_id ? route('prof.devoir.index', ['course_id' => $course_id]) : route('prof.devoir.index');
        return redirect($redirectRoute)->with('success', 'Devoir supprimé!');
    }
}
?>

