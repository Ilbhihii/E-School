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
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\URL;
use App\Services\LearningPathService;

class CourseController extends Controller
{
    private LearningPathService $paths;

    public function __construct(LearningPathService $paths)
    {
        $this->paths = $paths;
    }
    // ================= SHOW =================
    public function show($id)
    {
        $course = Course::with(['classRoom', 'subject', 'devoirs'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $course->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        $resourceUrls = [];
        foreach (['video', 'pdf', 'link'] as $type) {
            $exists = $type === 'video' ? ($course->video || $course->video_url) : ($type === 'pdf' ? $course->pdf : $course->course_link);
            if ($exists) {
                $resourceUrls[$type] = URL::temporarySignedRoute('course.resource', now()->addMinutes(10), ['course' => $course->id, 'type' => $type]);
            }
        }

        return view('admin.courses.show', compact('course', 'resourceUrls'));
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
        /*
         * La hiérarchie envoyée à la vue respecte désormais :
         *
         * Matière → Niveau appartenant à la matière
         *         → Classe appartenant au niveau et liée à la matière
         */
        $courseHierarchy = $this->buildCourseHierarchy();

        $subjects = collect($courseHierarchy)
            ->map(
                fn (array $subject) =>
                    (object) [
                        'id' => $subject['id'],
                        'name' => $subject['name'],
                    ]
            )
            ->values();

        $selectedSubjectId = $request->get(
            'subject_id'
        );

        $selectedLevelId = $request->get(
            'level_id'
        );

        $selectedClassId = $request->get(
            'class_id'
        );

        /*
         * Certaines pages ouvrent la création avec seulement class_id.
         * Dans ce cas, le niveau est déduit pour préremplir le formulaire.
         */
        if (
            $selectedClassId
            && !$selectedLevelId
        ) {
            $selectedLevelId = ClassRoom::query()
                ->whereKey($selectedClassId)
                ->value('level_id');
        }

        return view(
            'admin.courses.create',
            compact(
                'subjects',
                'courseHierarchy',
                'selectedSubjectId',
                'selectedLevelId',
                'selectedClassId'
            )
        );
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        if (
            !in_array(
                auth()->user()->role,
                ['admin', 'prof'],
                true
            )
        ) {
            abort(403);
        }

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
                'exists:subjects,id',
            ],
            'level_id' => [
                'required',
                'exists:levels,id',
            ],
            'class_id' => [
                'required',
                'exists:class_rooms,id',
            ],
            'course_link' => [
                'nullable',
                'url',
            ],
            'video' => [
                'nullable',
                'file',
                'mimes:mp4,mov,avi',
                'max:1048576',
            ],
            'pdf' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:1048576',
            ],
        ], [
            'subject_id.required' =>
                'Veuillez sélectionner une matière.',
            'level_id.required' =>
                'Veuillez sélectionner un niveau.',
            'class_id.required' =>
                'Veuillez sélectionner une classe.',
            'video.max' =>
                'La vidéo ne doit pas dépasser 1 Go.',
            'pdf.max' =>
                'Le document PDF ne doit pas dépasser 1 Go.',
        ]);

        $subject = Subject::findOrFail(
            $validated['subject_id']
        );

        $level = Level::findOrFail(
            $validated['level_id']
        );

        $classRoom = ClassRoom::with([
                'level',
                'subjects',
            ])
            ->findOrFail(
                $validated['class_id']
            );

        /*
         * Vérification 1 :
         * le niveau doit réellement appartenir à la matière choisie.
         */
        if (
            (int) $level->subject_id
            !== (int) $subject->id
        ) {
            throw ValidationException::withMessages([
                'level_id' =>
                    'Le niveau sélectionné n’appartient pas '
                    . 'à cette matière.',
            ]);
        }

        /*
         * Vérification 2 :
         * la classe doit réellement appartenir au niveau choisi.
         */
        if (
            (int) $classRoom->level_id
            !== (int) $level->id
        ) {
            throw ValidationException::withMessages([
                'class_id' =>
                    'La classe sélectionnée n’appartient pas '
                    . 'à ce niveau.',
            ]);
        }

        /*
         * Vérification 3 :
         * la classe doit être liée à la matière dans le pivot.
         */
        if (
            !$classRoom->subjects->contains(
                'id',
                (int) $subject->id
            )
        ) {
            throw ValidationException::withMessages([
                'class_id' =>
                    'Cette classe n’est pas liée à la matière '
                    . 'sélectionnée.',
            ]);
        }

        /*
         * Un professeur ne peut utiliser que ses affectations.
         */
        if (!auth()->user()->isAdmin()) {
            $isAssigned = ProfAssignment::query()
                ->where(
                    'prof_id',
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
                    $classRoom->id
                )
                ->exists();

            abort_unless(
                $isAssigned,
                403,
                'Cette structure ne fait pas partie '
                . 'de vos affectations.'
            );
        }

        $videoPath = null;
        $pdfPath = null;

        if ($request->hasFile('video')) {
            $videoPath = $request
                ->file('video')
                ->store(
                    'course-resources/video',
                    'local'
                );
        }

        if ($request->hasFile('pdf')) {
            $pdfPath = $request
                ->file('pdf')
                ->store(
                    'course-resources/pdf',
                    'local'
                );
        }

        Course::create([
            'title' => $validated['title'],
            'description' =>
                $validated['description'] ?? null,
            'subject_id' => $subject->id,
            'level_id' => $level->id,
            'class_id' => $classRoom->id,
            'video' => $videoPath,
            'pdf' => $pdfPath,
            'course_link' =>
                $validated['course_link'] ?? null,
            'admin_id' => auth()->id(),
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.courses.index')
            ->with(
                'success',
                'Cours créé avec succès.'
            );
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

        $resourceUrls = [];
        foreach (['video', 'pdf', 'link'] as $type) {
            $exists = $type === 'video'
                ? ($course->video || $course->video_url)
                : ($type === 'pdf' ? $course->pdf : $course->course_link);
            if ($exists) {
                $resourceUrls[$type] = URL::temporarySignedRoute(
                    'course.resource',
                    now()->addMinutes(10),
                    ['course' => $course->id, 'type' => $type]
                );
            }
        }

        return view('admin.courses.edit', compact(
            'course',
            'levels',
            'classes',
            'subjects',
            'resourceUrls'
        ));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $this->authorizeCourseOwner($course);

        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'class_id' => 'required|integer|exists:class_rooms,id',
            'level_id' => 'required|integer|exists:levels,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'course_link' => 'nullable|url',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:1048576',
            'pdf' => 'nullable|file|mimes:pdf|max:1048576'
        ]);

        [$subject, $level, $classRoom] = $this->paths->validatePath(
            (int) $data['subject_id'],
            (int) $data['level_id'],
            (int) $data['class_id']
        );

        if (!auth()->user()->isAdmin()) {
            abort_unless(
                $this->paths->professorCanAccessPath(
                    auth()->user(),
                    $subject->id,
                    $level->id,
                    $classRoom->id
                ),
                403
            );
        }

        $data['subject_id'] = $subject->id;
        $data['level_id'] = $level->id;
        $data['class_id'] = $classRoom->id;

        if ($request->hasFile('video')) {
            if ($course->video) {
                Storage::disk('local')->delete($course->video);
                Storage::disk('public')->delete($course->video);
            }
            $data['video'] = $request->file('video')->store('course-resources/video', 'local');
        }

        if ($request->hasFile('pdf')) {
            if ($course->pdf) {
                Storage::disk('local')->delete($course->pdf);
                Storage::disk('public')->delete($course->pdf);
            }
            $data['pdf'] = $request->file('pdf')->store('course-resources/pdf', 'local');
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

    /**
     * Construit les choix autorisés du formulaire :
     * Matière → Niveaux → Classes.
     */
    private function buildCourseHierarchy(): array
    {
        if (auth()->user()->isAdmin()) {
            $subjects = Subject::query()
                ->orderBy('name')
                ->get();

            $levels = Level::query()
                ->with([
                    'classes.subjects',
                ])
                ->orderBy('order')
                ->orderBy('name')
                ->get();

            return $subjects
                ->map(
                    function (
                        Subject $subject
                    ) use ($levels) {
                        $subjectLevels = $levels
                            ->where(
                                'subject_id',
                                $subject->id
                            )
                            ->map(
                                function (
                                    Level $level
                                ) use ($subject) {
                                    $classes = $level
                                        ->classes
                                        ->filter(
                                            fn (
                                                ClassRoom $classRoom
                                            ) =>
                                                $classRoom
                                                    ->subjects
                                                    ->contains(
                                                        'id',
                                                        $subject->id
                                                    )
                                        )
                                        ->sortBy('name')
                                        ->values()
                                        ->map(
                                            fn (
                                                ClassRoom $classRoom
                                            ) => [
                                                'id' =>
                                                    $classRoom->id,
                                                'name' =>
                                                    $classRoom->name,
                                            ]
                                        )
                                        ->all();

                                    if (empty($classes)) {
                                        return null;
                                    }

                                    return [
                                        'id' => $level->id,
                                        'name' => $level->name,
                                        'classes' => $classes,
                                    ];
                                }
                            )
                            ->filter()
                            ->values()
                            ->all();

                        if (empty($subjectLevels)) {
                            return null;
                        }

                        return [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'levels' => $subjectLevels,
                        ];
                    }
                )
                ->filter()
                ->values()
                ->all();
        }

        /*
         * Pour un professeur, la hiérarchie ne contient que les
         * affectations exactes enregistrées dans prof_assignments.
         */
        $assignments = ProfAssignment::query()
            ->where(
                'prof_id',
                auth()->id()
            )
            ->get();

        $subjects = Subject::query()
            ->whereIn(
                'id',
                $assignments->pluck('subject_id')
            )
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->whereIn(
                'id',
                $assignments->pluck('level_id')
            )
            ->with([
                'classes.subjects',
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return $subjects
            ->map(
                function (
                    Subject $subject
                ) use (
                    $levels,
                    $assignments
                ) {
                    $subjectLevels = $levels
                        ->filter(
                            fn (Level $level) =>
                                $assignments->contains(
                                    fn ($assignment) =>
                                        (int) $assignment
                                            ->subject_id
                                            === (int) $subject->id
                                        && (int) $assignment
                                            ->level_id
                                            === (int) $level->id
                                )
                        )
                        ->map(
                            function (
                                Level $level
                            ) use (
                                $subject,
                                $assignments
                            ) {
                                $classes = $level
                                    ->classes
                                    ->filter(
                                        fn (
                                            ClassRoom $classRoom
                                        ) =>
                                            $assignments->contains(
                                                fn ($assignment) =>
                                                    (int) $assignment
                                                        ->subject_id
                                                        === (int)
                                                            $subject->id
                                                    && (int) $assignment
                                                        ->level_id
                                                        === (int)
                                                            $level->id
                                                    && (int) $assignment
                                                        ->class_id
                                                        === (int)
                                                            $classRoom->id
                                            )
                                    )
                                    ->sortBy('name')
                                    ->values()
                                    ->map(
                                        fn (
                                            ClassRoom $classRoom
                                        ) => [
                                            'id' =>
                                                $classRoom->id,
                                            'name' =>
                                                $classRoom->name,
                                        ]
                                    )
                                    ->all();

                                if (empty($classes)) {
                                    return null;
                                }

                                return [
                                    'id' => $level->id,
                                    'name' => $level->name,
                                    'classes' => $classes,
                                ];
                            }
                        )
                        ->filter()
                        ->values()
                        ->all();

                    if (empty($subjectLevels)) {
                        return null;
                    }

                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'levels' => $subjectLevels,
                    ];
                }
            )
            ->filter()
            ->values()
            ->all();
    }

    private function authorizeCourseOwner(Course $course): void
    {
        if (! auth()->user()->isAdmin()) {
            abort_unless((int) $course->user_id === (int) auth()->id(), 403, 'Accès non autorisé');
        }
    }
}
