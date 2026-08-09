<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\ClassRoom;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use App\Services\PedagogicalStructureService;
use Illuminate\Validation\ValidationException;

class DevoirController extends Controller
{
    private PedagogicalStructureService $structure;

    public function __construct(
        PedagogicalStructureService $structure
    ) {
        $this->structure = $structure;
    }

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
        $devoir->load([
            'course',
            'subject',
            'classSlot.subject',
            'classSlot.level',
            'classSlot.classRoom',
        ]);

        $editHierarchy =
            $this->structure
                ->hierarchyForAdmin();

        $courses = Course::query()
            ->whereNotNull('slot_code')
            ->where('slot_code', '!=', '')
            ->orderBy('title')
            ->get();

        return view(
            'admin.devoirs.edit',
            [
                'devoir' => $devoir,
                'courses' => $courses,
                'editHierarchy' =>
                    $editHierarchy,
                'selectedSubjectId' =>
                    old(
                        'subject_id',
                        $devoir
                            ->classSlot
                            ?->subject_id
                        ?? $devoir->subject_id
                        ?? $devoir
                            ->course
                            ?->subject_id
                    ),
                'selectedLevelId' =>
                    old(
                        'level_id',
                        $devoir
                            ->classSlot
                            ?->level_id
                        ?? $devoir
                            ->course
                            ?->level_id
                    ),
                'selectedClassId' =>
                    old(
                        'class_id',
                        $devoir
                            ->classSlot
                            ?->class_id
                        ?? $devoir
                            ->class_room_id
                        ?? $devoir
                            ->course
                            ?->class_id
                    ),
                'selectedSlotId' =>
                    old(
                        'class_slot_id',
                        $devoir->class_slot_id
                    ),
            ]
        );
    }

    public function update(
        Request $request,
        Assignment $devoir
    ) {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'level_id' => [
                'required',
                'integer',
                'exists:levels,id',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],
            'class_slot_id' => [
                'required',
                'integer',
                'exists:class_slots,id',
            ],
            'course_id' => [
                'nullable',
                'integer',
                'exists:courses,id',
            ],
            'due_date' => [
                'required',
                'date',
            ],
            'file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120',
            ],
        ]);

        $slot =
            $this->structure
                ->slotForPath(
                    (int) $validated['class_slot_id'],
                    (int) $validated['subject_id'],
                    (int) $validated['level_id'],
                    (int) $validated['class_id']
                );

        $course = null;

        if (!empty($validated['course_id'])) {
            $course = Course::query()
                ->findOrFail(
                    (int) $validated['course_id']
                );

            if (
                !$this->structure
                    ->courseMatchesSlot(
                        $course,
                        $slot
                    )
            ) {
                throw ValidationException::withMessages([
                    'course_id' =>
                        'Le cours sélectionné ne correspond pas '
                        . 'au créneau '
                        . $slot->code
                        . '.',
                ]);
            }
        }

        if ($request->hasFile('file')) {
            if ($devoir->file) {
                Storage::disk('public')
                    ->delete(
                        $devoir->file
                    );
            }

            $devoir->file =
                $request
                    ->file('file')
                    ->store(
                        'assignments',
                        'public'
                    );
        }

        $devoir->title =
            $validated['title'];

        $devoir->description =
            $validated['description']
            ?? null;

        $devoir->due_date =
            $validated['due_date'];

        $devoir->subject_id =
            $slot->subject_id;

        $devoir->class_room_id =
            $slot->class_id;

        $devoir->class_slot_id =
            $slot->id;

        $devoir->course_id =
            $course?->id;

        $devoir->save();

        return redirect()
            ->route(
                'admin.devoirs.index',
                [
                    'course_id' =>
                        $devoir->course_id,
                ]
            )
            ->with(
                'success',
                'Devoir mis à jour pour le créneau '
                . $slot->code
                . '.'
            );
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