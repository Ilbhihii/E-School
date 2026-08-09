<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\Schedule;
use Illuminate\Support\Facades\Storage;
use App\Models\ProfAssignment;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Services\LearningPathService;
use App\Services\PedagogicalStructureService;

class CourseController extends Controller
{
    private LearningPathService $paths;
    private PedagogicalStructureService $structure;

    public function __construct(
        LearningPathService $paths,
        PedagogicalStructureService $structure
    ) {
        $this->paths = $paths;
        $this->structure = $structure;
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
    public function index(Request $request)
    {
        $status = $request->query('status');

        if (
            $status
            && !in_array(
                $status,
                [
                    Course::STATUS_PENDING,
                    Course::STATUS_APPROVED,
                    Course::STATUS_REJECTED,
                ],
                true
            )
        ) {
            $status = null;
        }

        $query = Course::query()
            ->with([
                'classRoom',
                'subject',
                'level',
                'assignments',
                'creator',
                'reviewer',
            ])
            ->latest();

        if ($status) {
            $query->where(
                'approval_status',
                $status
            );
        }

        $courses = $query
            ->paginate(12)
            ->appends(
                $request->query()
            );

        $courseStats = [
            'all' => Course::query()->count(),
            'pending' => Course::pending()->count(),
            'approved' => Course::approved()->count(),
            'rejected' => Course::rejected()->count(),
        ];

        return view(
            'admin.courses.index',
            compact(
                'courses',
                'courseStats',
                'status'
            )
        );
    }

    // ================= CREATE =================
    public function create(Request $request)
    {
        /*
         * La hiérarchie envoyée à la vue respecte désormais :
         *
         * Matière → Niveau appartenant à la matière
         *         → Classe appartenant au niveau et liée à la matière
         *         → Créneau / groupe
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

        $selectedSlotCode = $request->get(
            'slot_code'
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
                'selectedClassId',
                'selectedSlotCode'
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
            'slot_code' => [
                'required',
                'string',
                'max:20',
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
                'max:51200',
            ],
        ], [
            'subject_id.required' =>
                'Veuillez sélectionner une matière.',
            'level_id.required' =>
                'Veuillez sélectionner un niveau.',
            'class_id.required' =>
                'Veuillez sélectionner une classe.',
            'slot_code.required' =>
                'Veuillez sélectionner un créneau.',
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

        $slotCode = strtoupper(
            trim((string) $validated['slot_code'])
        );

        if (!in_array(
            $slotCode,
            $this->slotCodesForClass($classRoom),
            true
        )) {
            throw ValidationException::withMessages([
                'slot_code' =>
                    'Ce créneau ne correspond pas à la classe sélectionnée.',
            ]);
        }

        $classSlot =
            $this->structure
                ->slotByCodeForPath(
                    $slotCode,
                    (int) $subject->id,
                    (int) $level->id,
                    (int) $classRoom->id
                );

        /*
         * Ce contrôleur reste compatible avec un éventuel appel professeur,
         * mais les routes /admin/courses sont désormais réservées à l'admin.
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
                ->where(
                    'class_slot_id',
                    $classSlot->id
                )
                ->exists();

            abort_unless(
                $isAssigned,
                403,
                'Ce créneau ne fait pas partie '
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
            'slot_code' => $slotCode,
            'video' => $videoPath,
            'pdf' => $pdfPath,
            'course_link' =>
                $validated['course_link'] ?? null,
            'admin_id' => auth()->id(),
            'user_id' => auth()->id(),

            /*
             * Un cours créé directement par l'administration est
             * publié immédiatement : l'admin n'a pas à se valider.
             */
            'approval_status' =>
                Course::STATUS_APPROVED,
            'submitted_at' => now(),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('admin.courses.index')
            ->with(
                'success',
                'Cours créé et publié avec succès.'
            );
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $course = Course::query()
            ->with([
                'subject',
                'level',
                'classRoom',
            ])
            ->findOrFail($id);

        $this->authorizeCourseOwner($course);

        $editHierarchy =
            $this->structure
                ->hierarchyForAdmin();

        /*
         * Si l'éditeur est un professeur, limiter le formulaire
         * aux créneaux qui lui sont réellement affectés.
         */
        if (!auth()->user()->isAdmin()) {
            $allowedSlotIds = ProfAssignment::query()
                ->where('prof_id', auth()->id())
                ->whereNotNull('class_slot_id')
                ->pluck('class_slot_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $editHierarchy = collect($editHierarchy)
                ->map(function (array $subject) use ($allowedSlotIds) {
                    $subject['levels'] = collect($subject['levels'])
                        ->map(function (array $level) use ($allowedSlotIds) {
                            $level['classes'] = collect($level['classes'])
                                ->map(function (array $class) use ($allowedSlotIds) {
                                    $class['slots'] = collect($class['slots'])
                                        ->filter(
                                            fn (array $slot) =>
                                                in_array(
                                                    (int) $slot['id'],
                                                    $allowedSlotIds,
                                                    true
                                                )
                                        )
                                        ->values()
                                        ->all();

                                    return !empty($class['slots'])
                                        ? $class
                                        : null;
                                })
                                ->filter()
                                ->values()
                                ->all();

                            return !empty($level['classes'])
                                ? $level
                                : null;
                        })
                        ->filter()
                        ->values()
                        ->all();

                    return !empty($subject['levels'])
                        ? $subject
                        : null;
                })
                ->filter()
                ->values()
                ->all();
        }

        $currentSlot = null;

        if (
            $course->subject_id
            && $course->level_id
            && $course->class_id
            && trim((string) $course->slot_code) !== ''
        ) {
            try {
                $currentSlot =
                    $this->structure
                        ->slotByCodeForPath(
                            (string) $course->slot_code,
                            (int) $course->subject_id,
                            (int) $course->level_id,
                            (int) $course->class_id
                        );
            } catch (ValidationException $exception) {
                $currentSlot = null;
            }
        }

        $resourceUrls = [];

        foreach (['video', 'pdf', 'link'] as $type) {
            $exists = $type === 'video'
                ? ($course->video || $course->video_url)
                : (
                    $type === 'pdf'
                        ? $course->pdf
                        : $course->course_link
                );

            if ($exists) {
                $resourceUrls[$type] =
                    URL::temporarySignedRoute(
                        'course.resource',
                        now()->addMinutes(10),
                        [
                            'course' => $course->id,
                            'type' => $type,
                        ]
                    );
            }
        }

        return view(
            'admin.courses.edit',
            compact(
                'course',
                'editHierarchy',
                'currentSlot',
                'resourceUrls'
            )
        );
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $this->authorizeCourseOwner($course);

        /*
         * Le niveau affiché dans le formulaire est automatiquement déduit
         * de la classe. On accepte level_id pour réafficher correctement les
         * erreurs, mais la valeur fiable est reprise depuis la classe en base.
         */
        $data = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],
            'level_id' => [
                'nullable',
                'integer',
                'exists:levels,id',
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'slot_code' => [
                'required',
                'string',
                'max:20',
            ],
            'course_link' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'video' => [
                'nullable',
                'file',
                'mimes:mp4,mov,avi,webm,m4v',
                'max:1048576',
            ],
            'pdf' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:51200',
            ],
        ], [
            'title.required' =>
                'Le titre du cours est obligatoire.',
            'class_id.required' =>
                'Veuillez sélectionner une classe.',
            'subject_id.required' =>
                'Veuillez sélectionner une matière.',
            'slot_code.required' =>
                'Veuillez sélectionner un créneau.',
            'video.max' =>
                'La vidéo ne doit pas dépasser 1 Go.',
            'video.mimes' =>
                'La vidéo doit être au format MP4, MOV, AVI, WEBM ou M4V.',
            'pdf.max' =>
                'Le document PDF ne doit pas dépasser 50 Mo.',
            'pdf.mimes' =>
                'Le document sélectionné doit être un fichier PDF.',
        ]);

        $classForLevel = ClassRoom::query()
            ->findOrFail((int) $data['class_id']);

        $resolvedLevelId = (int) $classForLevel->level_id;

        [$subject, $level, $classRoom] = $this->paths->validatePath(
            (int) $data['subject_id'],
            $resolvedLevelId,
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
                403,
                'Cette structure ne fait pas partie '
                . 'de vos affectations.'
            );
        }

        $slotCode = strtoupper(
            trim((string) $data['slot_code'])
        );

        $classSlot =
            $this->structure
                ->slotByCodeForPath(
                    $slotCode,
                    (int) $subject->id,
                    (int) $level->id,
                    (int) $classRoom->id
                );

        if (!auth()->user()->isAdmin()) {
            $hasExactAssignment =
                ProfAssignment::query()
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
                    ->where(
                        'class_slot_id',
                        $classSlot->id
                    )
                    ->exists();

            abort_unless(
                $hasExactAssignment,
                403,
                'Ce créneau ne fait pas partie de vos affectations.'
            );
        }

        $oldVideoPath = $course->video;
        $oldPdfPath = $course->pdf;
        $newVideoPath = null;
        $newPdfPath = null;

        /*
         * Les objets UploadedFile ne doivent pas être envoyés directement à
         * Course::update(). Ils sont remplacés par les chemins enregistrés.
         */
        unset($data['video'], $data['pdf']);

        $data['subject_id'] = $subject->id;
        $data['level_id'] = $level->id;
        $data['class_id'] = $classRoom->id;
        $data['slot_code'] = $slotCode;

        try {
            if ($request->hasFile('video')) {
                $newVideoPath = $request
                    ->file('video')
                    ->store(
                        'course-resources/video',
                        'local'
                    );

                if (!$newVideoPath) {
                    throw ValidationException::withMessages([
                        'video' =>
                            'La nouvelle vidéo n’a pas pu être enregistrée.',
                    ]);
                }

                $data['video'] = $newVideoPath;
            }

            if ($request->hasFile('pdf')) {
                $newPdfPath = $request
                    ->file('pdf')
                    ->store(
                        'course-resources/pdf',
                        'local'
                    );

                if (!$newPdfPath) {
                    throw ValidationException::withMessages([
                        'pdf' =>
                            'Le nouveau PDF n’a pas pu être enregistré.',
                    ]);
                }

                $data['pdf'] = $newPdfPath;
            }

            $course->update($data);
        } catch (\Throwable $exception) {
            /*
             * En cas d’échec de la base de données, les nouveaux fichiers
             * incomplets sont supprimés et les anciens restent intacts.
             */
            if ($newVideoPath) {
                Storage::disk('local')->delete($newVideoPath);
            }

            if ($newPdfPath) {
                Storage::disk('local')->delete($newPdfPath);
            }

            throw $exception;
        }

        /*
         * Les anciens fichiers ne sont supprimés qu’après la réussite de la
         * mise à jour. Cela évite de perdre le cours en cas d’erreur d’envoi.
         */
        if (
            $newVideoPath
            && $oldVideoPath
            && $oldVideoPath !== $newVideoPath
        ) {
            Storage::disk('local')->delete($oldVideoPath);
            Storage::disk('public')->delete($oldVideoPath);
        }

        if (
            $newPdfPath
            && $oldPdfPath
            && $oldPdfPath !== $newPdfPath
        ) {
            Storage::disk('local')->delete($oldPdfPath);
            Storage::disk('public')->delete($oldPdfPath);
        }

        return redirect()
            ->route('admin.courses.index')
            ->with(
                'success',
                'Le cours a été mis à jour avec succès.'
            );
    }

    // ================= VALIDATION ADMIN =================
    public function approve(Course $course)
    {
        $course->loadMissing('creator');

        abort_unless(
            $course->creator
            && $course->creator->isProf(),
            422,
            'Ce cours a été créé directement par l’administration.'
        );

        $course->update([
            'approval_status' =>
                Course::STATUS_APPROVED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with(
            'success',
            'Le cours « '
            . $course->title
            . ' » est maintenant publié.'
        );
    }

    public function reject(
        Request $request,
        Course $course
    ) {
        $course->loadMissing('creator');

        abort_unless(
            $course->creator
            && $course->creator->isProf(),
            422,
            'Ce cours a été créé directement par l’administration.'
        );

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'min:3',
                'max:2000',
            ],
        ], [
            'rejection_reason.required' =>
                'Veuillez indiquer le motif du refus.',
        ]);

        $course->update([
            'approval_status' =>
                Course::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' =>
                $validated['rejection_reason'],
        ]);

        return back()->with(
            'success',
            'Le cours « '
            . $course->title
            . ' » a été refusé.'
        );
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

        // Supprimer les ressources, qu'elles soient anciennes (public)
        // ou nouvelles (stockage privé local).
        foreach (
            [$course->video, $course->pdf]
            as $path
        ) {
            if (!$path) {
                continue;
            }

            Storage::disk('local')
                ->delete($path);

            Storage::disk('public')
                ->delete($path);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Cours supprimé avec succès');
    }

    /**
     * Construit les choix autorisés du formulaire :
     * Matière → Niveaux → Classes → Créneaux.
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

            $scheduleGroups = Schedule::query()
                ->active()
                ->orderByRaw('COALESCE(day_of_week, 8) asc')
                ->orderByRaw('TIME(start_time) asc')
                ->get()
                ->groupBy(
                    fn (Schedule $schedule) =>
                        (int) $schedule->subject_id
                        . ':' . (int) $schedule->level_id
                        . ':' . (int) $schedule->class_id
                        . ':' . strtoupper(trim((string) $schedule->slot_code))
                );

            return $subjects
                ->map(
                    function (Subject $subject) use ($levels, $scheduleGroups) {
                        $subjectLevels = $levels
                            ->where(
                                'subject_id',
                                $subject->id
                            );

                        /*
                         * La base contient encore d'anciens parcours Arabe.
                         * Ils ne doivent plus apparaître dans le formulaire.
                         */
                        $allowedLevelNames =
                            $this->allowedLevelNamesForSubject(
                                $subject
                            );

                        if ($allowedLevelNames !== null) {
                            $subjectLevels = $subjectLevels
                                ->filter(
                                    fn (Level $level) =>
                                        in_array(
                                            $this->normalizePathName(
                                                $level->name
                                            ),
                                            $allowedLevelNames,
                                            true
                                        )
                                );
                        }

                        /*
                         * Évite d'afficher deux fois un parcours portant
                         * le même nom lorsqu'un ancien doublon existe.
                         */
                        $subjectLevels = $subjectLevels
                            ->unique(
                                fn (Level $level) =>
                                    $this->normalizePathName(
                                        $level->name
                                    )
                            )
                            ->values()
                            ->map(
                                function (Level $level) use ($subject, $scheduleGroups) {
                                    $classes = $level
                                        ->classes
                                        ->filter(
                                            fn (ClassRoom $classRoom) =>
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
                                            function (ClassRoom $classRoom) use (
                                                $subject,
                                                $level,
                                                $scheduleGroups
                                            ) {
                                                $slots = collect(
                                                    $this->slotCodesForClass($classRoom)
                                                )->map(
                                                    function (string $code) use (
                                                        $subject,
                                                        $level,
                                                        $classRoom,
                                                        $scheduleGroups
                                                    ) {
                                                        $key = (int) $subject->id
                                                            . ':' . (int) $level->id
                                                            . ':' . (int) $classRoom->id
                                                            . ':' . $code;

                                                        $schedules = $scheduleGroups
                                                            ->get($key, collect());

                                                        $scheduleLabel = $schedules
                                                            ->map(
                                                                fn (Schedule $schedule) =>
                                                                    $schedule->day_label
                                                                    . ' '
                                                                    . $schedule->time_range_label
                                                            )
                                                            ->implode(' / ');

                                                        return [
                                                            'code' => $code,
                                                            'label' => $scheduleLabel !== ''
                                                                ? $code . ' — ' . $scheduleLabel
                                                                : $code,
                                                        ];
                                                    }
                                                )->values()->all();

                                                return [
                                                    'id' => $classRoom->id,
                                                    'name' => $classRoom->name,
                                                    'slots' => $slots,
                                                ];
                                            }
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
                function (Subject $subject) use (
                    $levels,
                    $assignments
                ) {
                    $subjectLevels = $levels
                        ->filter(
                            fn (Level $level) =>
                                $assignments->contains(
                                    fn ($assignment) =>
                                        (int) $assignment->subject_id
                                            === (int) $subject->id
                                        && (int) $assignment->level_id
                                            === (int) $level->id
                                )
                        );

                    $allowedLevelNames =
                        $this->allowedLevelNamesForSubject(
                            $subject
                        );

                    if ($allowedLevelNames !== null) {
                        $subjectLevels = $subjectLevels
                            ->filter(
                                fn (Level $level) =>
                                    in_array(
                                        $this->normalizePathName(
                                            $level->name
                                        ),
                                        $allowedLevelNames,
                                        true
                                    )
                            );
                    }

                    $subjectLevels = $subjectLevels
                        ->unique(
                            fn (Level $level) =>
                                $this->normalizePathName(
                                    $level->name
                                )
                        )
                        ->values()
                        ->map(
                            function (Level $level) use (
                                $subject,
                                $assignments
                            ) {
                                $classes = $level
                                    ->classes
                                    ->filter(
                                        fn (ClassRoom $classRoom) =>
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
                                        fn (ClassRoom $classRoom) => [
                                            'id' => $classRoom->id,
                                            'name' => $classRoom->name,
                                            'slots' => collect(
                                                $this->slotCodesForClass($classRoom)
                                            )->map(
                                                fn (string $code) => [
                                                    'code' => $code,
                                                    'label' => $code,
                                                ]
                                            )->values()->all(),
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

    /**
     * Chaque classe possède quatre créneaux / groupes.
     */
    private function slotCodesForClass(
        ClassRoom $classRoom
    ): array {
        $normalized = $this->normalizePathName($classRoom->name);

        $prefix = match (true) {
            str_contains($normalized, 'debutant') => 'D',
            str_contains($normalized, 'intermediaire') => 'I',
            str_contains($normalized, 'avance') => 'A',
            default => 'G',
        };

        return collect(range(1, 4))
            ->map(fn (int $number) => $prefix . $number)
            ->all();
    }

    /**
     * Liste officielle des parcours actuellement utilisés.
     * null signifie : ne pas appliquer de filtre spécial à la matière.
     */
    private function allowedLevelNamesForSubject(
        Subject $subject
    ): ?array {
        return match (
            $this->normalizePathName($subject->name)
        ) {
            'arabe' => [
                'lecture & ecriture',
                'communication',
            ],
            'coran' => [
                'apprentissage & tajwid',
            ],
            'soutien lycee' => [
                'bac',
            ],
            default => null,
        };
    }

    /**
     * Uniformise accents, majuscules et espaces pour comparer les noms.
     */
    private function normalizePathName(
        string $value
    ): string {
        $value = preg_replace(
            '/\\s+/u',
            ' ',
            trim($value)
        );

        return Str::lower(
            Str::ascii((string) $value)
        );
    }

    private function authorizeCourseOwner(Course $course): void
    {
        if (! auth()->user()->isAdmin()) {
            abort_unless((int) $course->user_id === (int) auth()->id(), 403, 'Accès non autorisé');
        }
    }
}
