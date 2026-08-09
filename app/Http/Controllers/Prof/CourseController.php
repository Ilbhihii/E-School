<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ProfAssignment;
use App\Services\ProfessorPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    private ProfessorPathService $profPaths;

    public function __construct(
        ProfessorPathService $profPaths
    ) {
        $this->profPaths = $profPaths;
    }

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
            ->where(
                'user_id',
                auth()->id()
            )
            ->with([
                'subject',
                'level',
                'classRoom',
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

        $stats = [
            'all' => Course::query()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->count(),

            'pending' => Course::query()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->pending()
                ->count(),

            'approved' => Course::query()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->approved()
                ->count(),

            'rejected' => Course::query()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->rejected()
                ->count(),
        ];

        return view(
            'prof.courses.index',
            compact(
                'courses',
                'stats',
                'status'
            )
        );
    }

    public function create(Request $request)
    {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $selected = [
            'selectedSubjectId' =>
                old(
                    'subject_id',
                    $request->query(
                        'subject_id'
                    )
                ),
            'selectedLevelId' =>
                old(
                    'level_id',
                    $request->query(
                        'level_id'
                    )
                ),
            'selectedClassId' =>
                old(
                    'class_id',
                    $request->query(
                        'class_id'
                    )
                ),
            'selectedSlotId' =>
                old(
                    'class_slot_id',
                    $request->query(
                        'class_slot_id'
                    )
                ),
        ];

        return view(
            'prof.courses.create',
            array_merge(
                compact(
                    'profHierarchy'
                ),
                $selected
            )
        );
    }

    public function store(Request $request)
    {
        $validated =
            $this->validateCourse(
                $request
            );

        $assignment =
            $this->profPaths
                ->findExactAssignment(
                    auth()->id(),
                    (int) $validated[
                        'subject_id'
                    ],
                    (int) $validated[
                        'level_id'
                    ],
                    (int) $validated[
                        'class_id'
                    ],
                    (int) $validated[
                        'class_slot_id'
                    ]
                );

        abort_unless(
            $assignment
            && $assignment->classSlot,
            403,
            'Ce créneau ne fait pas partie de vos affectations.'
        );

        [$videoPath, $pdfPath] =
            $this->storeFiles(
                $request
            );

        try {
            $course = Course::create([
                'title' =>
                    $validated['title'],
                'description' =>
                    $validated['description']
                    ?? null,
                'subject_id' =>
                    $assignment->subject_id,
                'level_id' =>
                    $assignment->level_id,
                'class_id' =>
                    $assignment->class_id,
                'slot_code' =>
                    $assignment
                        ->classSlot
                        ->code,
                'video' =>
                    $videoPath,
                'pdf' =>
                    $pdfPath,
                'course_link' =>
                    $validated['course_link']
                    ?? null,
                'is_free' => false,

                /*
                 * Le professeur est le créateur.
                 * Le cours ne devient visible aux étudiants
                 * qu'après validation administrative.
                 */
                'user_id' => auth()->id(),
                'admin_id' => null,
                'approval_status' =>
                    Course::STATUS_PENDING,
                'submitted_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
                'rejection_reason' => null,
            ]);
        } catch (\Throwable $exception) {
            $this->deleteNewFiles(
                $videoPath,
                $pdfPath
            );

            throw $exception;
        }

        return redirect()
            ->route(
                'prof.courses.index'
            )
            ->with(
                'success',
                'Cours envoyé à l’administration. '
                . 'Il sera publié après validation.'
            );
    }

    public function show(Course $course)
    {
        $this->authorizeOwner(
            $course
        );

        $course->load([
            'subject',
            'level',
            'classRoom',
            'reviewer',
        ]);

        $resourceUrls = [];

        foreach (
            ['video', 'pdf', 'link']
            as $type
        ) {
            $exists =
                $type === 'video'
                    ? (
                        $course->video
                        || $course->video_url
                    )
                    : (
                        $type === 'pdf'
                            ? $course->pdf
                            : $course->course_link
                    );

            if (!$exists) {
                continue;
            }

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

        return view(
            'prof.courses.show',
            compact(
                'course',
                'resourceUrls'
            )
        );
    }

    public function edit(Course $course)
    {
        $this->authorizeOwner(
            $course
        );

        $course->load([
            'subject',
            'level',
            'classRoom',
        ]);

        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $currentAssignment =
            ProfAssignment::query()
                ->with('classSlot')
                ->where(
                    'prof_id',
                    auth()->id()
                )
                ->where(
                    'subject_id',
                    $course->subject_id
                )
                ->where(
                    'level_id',
                    $course->level_id
                )
                ->where(
                    'class_id',
                    $course->class_id
                )
                ->whereHas(
                    'classSlot',
                    function ($query) use ($course) {
                        $query->whereRaw(
                            'UPPER(TRIM(code)) = ?',
                            [
                                strtoupper(
                                    trim(
                                        (string)
                                        $course->slot_code
                                    )
                                ),
                            ]
                        );
                    }
                )
                ->first();

        return view(
            'prof.courses.edit',
            [
                'course' => $course,
                'profHierarchy' =>
                    $profHierarchy,
                'selectedSubjectId' =>
                    old(
                        'subject_id',
                        $course->subject_id
                    ),
                'selectedLevelId' =>
                    old(
                        'level_id',
                        $course->level_id
                    ),
                'selectedClassId' =>
                    old(
                        'class_id',
                        $course->class_id
                    ),
                'selectedSlotId' =>
                    old(
                        'class_slot_id',
                        $currentAssignment
                            ?->class_slot_id
                    ),
            ]
        );
    }

    public function update(
        Request $request,
        Course $course
    ) {
        $this->authorizeOwner(
            $course
        );

        $validated =
            $this->validateCourse(
                $request
            );

        $assignment =
            $this->profPaths
                ->findExactAssignment(
                    auth()->id(),
                    (int) $validated[
                        'subject_id'
                    ],
                    (int) $validated[
                        'level_id'
                    ],
                    (int) $validated[
                        'class_id'
                    ],
                    (int) $validated[
                        'class_slot_id'
                    ]
                );

        abort_unless(
            $assignment
            && $assignment->classSlot,
            403,
            'Ce créneau ne fait pas partie de vos affectations.'
        );

        $oldVideo =
            $course->video;

        $oldPdf =
            $course->pdf;

        [$newVideo, $newPdf] =
            $this->storeFiles(
                $request
            );

        try {
            $course->title =
                $validated['title'];

            $course->description =
                $validated['description']
                ?? null;

            $course->subject_id =
                $assignment->subject_id;

            $course->level_id =
                $assignment->level_id;

            $course->class_id =
                $assignment->class_id;

            $course->slot_code =
                $assignment
                    ->classSlot
                    ->code;

            $course->course_link =
                $validated['course_link']
                ?? null;

            if ($newVideo) {
                $course->video =
                    $newVideo;
            }

            if ($newPdf) {
                $course->pdf =
                    $newPdf;
            }

            /*
             * Toute modification effectuée par le professeur
             * doit être revalidée, y compris un cours déjà publié.
             */
            $course->approval_status =
                Course::STATUS_PENDING;

            $course->submitted_at =
                now();

            $course->reviewed_by =
                null;

            $course->reviewed_at =
                null;

            $course->rejection_reason =
                null;

            $course->save();
        } catch (\Throwable $exception) {
            $this->deleteNewFiles(
                $newVideo,
                $newPdf
            );

            throw $exception;
        }

        $this->deleteReplacedFile(
            $oldVideo,
            $newVideo
        );

        $this->deleteReplacedFile(
            $oldPdf,
            $newPdf
        );

        return redirect()
            ->route(
                'prof.courses.index'
            )
            ->with(
                'success',
                'Cours renvoyé à l’administration pour validation.'
            );
    }

    public function destroy(Course $course)
    {
        $this->authorizeOwner(
            $course
        );

        if ($course->isApproved()) {
            throw ValidationException::withMessages([
                'course' =>
                    'Un cours déjà publié ne peut pas être supprimé '
                    . 'par le professeur. Contactez l’administration.',
            ]);
        }

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

        return redirect()
            ->route(
                'prof.courses.index'
            )
            ->with(
                'success',
                'Proposition supprimée.'
            );
    }

    private function validateCourse(
        Request $request
    ): array {
        return $request->validate([
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
                'max:1048576',
            ],
        ], [
            'subject_id.required' =>
                'Veuillez sélectionner une matière.',
            'level_id.required' =>
                'Veuillez sélectionner un niveau.',
            'class_id.required' =>
                'Veuillez sélectionner une classe.',
            'class_slot_id.required' =>
                'Veuillez sélectionner un créneau.',
            'video.max' =>
                'La vidéo ne doit pas dépasser 1 Go.',
            'video.mimes' =>
                'La vidéo doit être au format MP4, MOV, AVI, WEBM ou M4V.',
            'pdf.max' =>
                'Le document PDF ne doit pas dépasser 1 Go.',
            'pdf.mimes' =>
                'Le document sélectionné doit être un fichier PDF.',
        ]);
    }

    private function authorizeOwner(
        Course $course
    ): void {
        abort_unless(
            (int) $course->user_id
                === (int) auth()->id(),
            403,
            'Ce cours ne vous appartient pas.'
        );
    }

    private function storeFiles(
        Request $request
    ): array {
        $video = null;
        $pdf = null;

        if (
            $request->hasFile('video')
        ) {
            $video = $request
                ->file('video')
                ->store(
                    'course-resources/video',
                    'local'
                );
        }

        if (
            $request->hasFile('pdf')
        ) {
            $pdf = $request
                ->file('pdf')
                ->store(
                    'course-resources/pdf',
                    'local'
                );
        }

        return [
            $video,
            $pdf,
        ];
    }

    private function deleteNewFiles(
        ?string $video,
        ?string $pdf
    ): void {
        foreach (
            [$video, $pdf]
            as $path
        ) {
            if ($path) {
                Storage::disk('local')
                    ->delete($path);
            }
        }
    }

    private function deleteReplacedFile(
        ?string $oldPath,
        ?string $newPath
    ): void {
        if (
            !$newPath
            || !$oldPath
            || $oldPath === $newPath
        ) {
            return;
        }

        Storage::disk('local')
            ->delete($oldPath);

        Storage::disk('public')
            ->delete($oldPath);
    }
}
