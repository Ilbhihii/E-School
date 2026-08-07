<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\ProfAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $course = $course_id
            ? Course::with(['subject', 'classRoom.level'])
                ->where('user_id', auth()->id())
                ->findOrFail($course_id)
            : null;

        $profAssignments = ProfAssignment::query()
            ->with(['subject', 'level', 'classRoom'])
            ->where('prof_id', auth()->id())
            ->orderBy('subject_id')
            ->orderBy('level_id')
            ->orderBy('class_id')
            ->get();

        $teachingPaths = $profAssignments
            ->filter(fn ($assignment) => $assignment->subject && $assignment->level && $assignment->classRoom)
            ->map(fn ($assignment) => [
                'subject_id' => (int) $assignment->subject_id,
                'subject_name' => $assignment->subject->name,
                'level_id' => (int) $assignment->level_id,
                'level_name' => $assignment->level->name,
                'class_id' => (int) $assignment->class_id,
                'class_name' => $assignment->classRoom->name,
            ])
            ->values();

        $courses = Course::query()
            ->with('classRoom')
            ->where('user_id', auth()->id())
            ->orderBy('title')
            ->get();

        $courseOptions = $courses->map(function ($courseOption) {
            return [
                'id' => (int) $courseOption->id,
                'title' => $courseOption->title,
                'subject_id' => (int) $courseOption->subject_id,
                'level_id' => (int) ($courseOption->level_id ?: optional($courseOption->classRoom)->level_id),
                'class_id' => (int) $courseOption->class_id,
            ];
        })->values();

        $selectedSubjectId = old('subject_id', $course?->subject_id);
        $selectedLevelId = old('level_id', $course?->level_id ?: $course?->classRoom?->level_id);
        $selectedClassId = old('class_room_id', $course?->class_id);
        $selectedCourseId = old('course_id', $course_id);

        return view('prof.devoir.create', compact(
            'course',
            'courses',
            'course_id',
            'teachingPaths',
            'courseOptions',
            'selectedSubjectId',
            'selectedLevelId',
            'selectedClassId',
            'selectedCourseId'
        ));
    }

    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'prof'])) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|integer|exists:subjects,id',
            'level_id' => 'required|integer|exists:levels,id',
            'class_room_id' => 'required|integer|exists:class_rooms,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'due_date' => 'required|date|after:today',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $scope = ProfAssignment::query()
            ->where('prof_id', auth()->id())
            ->where('subject_id', $validated['subject_id'])
            ->where('level_id', $validated['level_id'])
            ->where('class_id', $validated['class_room_id'])
            ->first();

        abort_unless($scope, 403);

        $class = ClassRoom::findOrFail($validated['class_room_id']);
        abort_unless((int) $class->level_id === (int) $validated['level_id'], 422);

        $course = null;
        if (!empty($validated['course_id'])) {
            $course = Course::query()
                ->with('classRoom')
                ->where('user_id', auth()->id())
                ->findOrFail($validated['course_id']);

            $courseLevelId = (int) ($course->level_id ?: optional($course->classRoom)->level_id);
            abort_unless(
                (int) $course->subject_id === (int) $validated['subject_id']
                && $courseLevelId === (int) $validated['level_id']
                && (int) $course->class_id === (int) $validated['class_room_id'],
                422
            );
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignments', 'public');
        }

        Assignment::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file' => $filePath,
            'due_date' => $validated['due_date'],
            'course_id' => $course?->id,
            'subject_id' => (int) $validated['subject_id'],
            'class_room_id' => (int) $validated['class_room_id'],
            'user_id' => auth()->id(),
        ]);

        $redirectRoute = $course
            ? route('prof.devoir.index', ['course_id' => $course->id])
            : route('prof.devoir.index');

        return redirect($redirectRoute)->with('success', 'Devoir créé avec succès !');
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

        abort_unless(
            ProfAssignment::where('prof_id', auth()->id())
                ->where('class_id', $request->class_room_id)
                ->exists(),
            403
        );

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

        $redirectRoute = $devoir->course_id
            ? route('prof.devoir.index', ['course_id' => $devoir->course_id])
            : route('prof.devoir.index');

        return redirect($redirectRoute)->with('success', 'Devoir mis à jour !');
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

        $redirectRoute = $course_id
            ? route('prof.devoir.index', ['course_id' => $course_id])
            : route('prof.devoir.index');

        return redirect($redirectRoute)->with('success', 'Devoir supprimé !');
    }
}
