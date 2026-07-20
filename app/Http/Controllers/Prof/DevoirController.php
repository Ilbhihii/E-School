<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\ClassRoom;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\ProfAssignment;

class DevoirController extends Controller
{
    public function index(Request $request)
    {
        $course_id = $request->course_id;
        $course = null;
        $query = Assignment::where('user_id', auth()->id());
        if ($course_id) {
            $query->where('course_id', $course_id);
            $course = Course::where('user_id', auth()->id())->findOrFail($course_id);
        }
        $devoirs = $query->orderBy('created_at', 'desc')->paginate(10);

        $courses = Course::where('user_id', auth()->id())->orderBy('title')->get();

        return view('prof.devoir.index', compact('devoirs', 'course_id', 'course', 'courses'));
    }


    public function create(Request $request)
    {
        $course_id = $request->course_id;
        $course = $course_id ? Course::where('user_id', auth()->id())->findOrFail($course_id) : null;
        $scope = ProfAssignment::where('prof_id', auth()->id())->get();
        $classes = ClassRoom::whereIn('id', $scope->pluck('class_id'))->orderBy('name')->get();
        $courses = Course::where('user_id', auth()->id())->orderBy('title')->get();
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
        $course = $request->course_id
            ? Course::where('user_id', auth()->id())->findOrFail($request->course_id)
            : null;
        abort_unless(ProfAssignment::where('prof_id', auth()->id())
            ->where('class_id', $request->class_room_id)
            ->when($course, fn ($query) => $query->where('subject_id', $course->subject_id))
            ->exists(), 403);

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

        $classIds = ProfAssignment::where('prof_id', auth()->id())->pluck('class_id');
        $classes = ClassRoom::whereIn('id', $classIds)->orderBy('name')->get();
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
        abort_unless(ProfAssignment::where('prof_id', auth()->id())
            ->where('class_id', $request->class_room_id)->exists(), 403);

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
