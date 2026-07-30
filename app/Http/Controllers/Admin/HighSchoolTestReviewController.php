<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HighSchoolTestSubmission;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HighSchoolTestReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = HighSchoolTestSubmission::query()
            ->with([
                'user',
                'subject',
                'level',
                'classRoom',
                'reviewer',
                'appointment',
            ])
            ->latest('submitted_at')
            ->latest('id');

        if (
            $request->filled('status')
            && $request->status !== 'all'
        ) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('subject_id')) {
            $query->where(
                'subject_id',
                $request->subject_id
            );
        }

        if ($request->filled('search')) {
            $search = trim(
                (string) $request->search
            );

            $query->where(
                function ($query) use ($search) {
                    $query
                        ->where(
                            'test_title',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'user',
                            function ($userQuery) use (
                                $search
                            ) {
                                $userQuery
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                }
            );
        }

        $submissions = $query
            ->paginate(16)
            ->withQueryString();

        $subjects = Subject::query()
            ->whereHas(
                'highSchoolTestSubmissions'
            )
            ->orderBy('name')
            ->get();

        $statistics = [
            'all' =>
                HighSchoolTestSubmission::count(),
            'submitted' =>
                HighSchoolTestSubmission::where(
                    'status',
                    HighSchoolTestSubmission
                        ::STATUS_SUBMITTED
                )->count(),
            'under_review' =>
                HighSchoolTestSubmission::where(
                    'status',
                    HighSchoolTestSubmission
                        ::STATUS_UNDER_REVIEW
                )->count(),
            'approved' =>
                HighSchoolTestSubmission::where(
                    'status',
                    HighSchoolTestSubmission
                        ::STATUS_APPROVED
                )->count(),
            'revision_requested' =>
                HighSchoolTestSubmission::where(
                    'status',
                    HighSchoolTestSubmission
                        ::STATUS_REVISION_REQUESTED
                )->count(),
            'rejected' =>
                HighSchoolTestSubmission::where(
                    'status',
                    HighSchoolTestSubmission
                        ::STATUS_REJECTED
                )->count(),
        ];

        return view(
            'admin.high-school-tests.index',
            compact(
                'submissions',
                'subjects',
                'statistics'
            )
        );
    }

    public function show(
        HighSchoolTestSubmission $submission
    ) {
        $submission->load([
            'user',
            'subject',
            'level',
            'classRoom',
            'reviewer',
            'appointment',
        ]);

        return view(
            'admin.high-school-tests.show',
            compact('submission')
        );
    }

    public function update(
        Request $request,
        HighSchoolTestSubmission $submission
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(
                    array_keys(
                        HighSchoolTestSubmission::statuses()
                    )
                ),
            ],
            'score' => [
                'nullable',
                'integer',
                'min:0',
                'max:20',
            ],
            'teacher_comment' => [
                'nullable',
                'string',
                'max:4000',
            ],
            'image_annotations' => [
                'nullable',
                'array',
            ],
            'image_annotations.*' => [
                'nullable',
                'string',
                'max:1500',
            ],
        ]);

        if (
            $validated['status']
            === HighSchoolTestSubmission
                ::STATUS_APPROVED
            && !isset($validated['score'])
        ) {
            throw ValidationException::withMessages([
                'score' =>
                    'Une note sur 20 est obligatoire '
                    . 'pour valider le test.',
            ]);
        }

        if (
            in_array(
                $validated['status'],
                [
                    HighSchoolTestSubmission
                        ::STATUS_REJECTED,
                    HighSchoolTestSubmission
                        ::STATUS_REVISION_REQUESTED,
                ],
                true
            )
            && empty(
                trim(
                    (string) (
                        $validated['teacher_comment']
                        ?? ''
                    )
                )
            )
        ) {
            throw ValidationException::withMessages([
                'teacher_comment' =>
                    'Expliquez à l’étudiant pourquoi '
                    . 'le test est refusé ou doit être refait.',
            ]);
        }

        $annotations = collect(
            $validated['image_annotations']
            ?? []
        )
            ->map(
                fn ($annotation) =>
                    trim((string) $annotation)
            )
            ->values()
            ->all();

        DB::transaction(
            function () use (
                $submission,
                $validated,
                $annotations
            ) {
                $isFinalStatus = in_array(
                    $validated['status'],
                    [
                        HighSchoolTestSubmission
                            ::STATUS_APPROVED,
                        HighSchoolTestSubmission
                            ::STATUS_REJECTED,
                        HighSchoolTestSubmission
                            ::STATUS_REVISION_REQUESTED,
                        HighSchoolTestSubmission
                            ::STATUS_REVIEWED,
                    ],
                    true
                );

                $submission->update([
                    'status' =>
                        $validated['status'],
                    'score' =>
                        $validated['score']
                        ?? null,
                    'teacher_comment' =>
                        $validated['teacher_comment']
                        ?? null,
                    'image_annotations' =>
                        $annotations,
                    'reviewed_by' =>
                        auth()->id(),
                    'reviewed_at' =>
                        $isFinalStatus
                            ? now()
                            : null,
                    'access_granted_at' =>
                        $validated['status']
                        === HighSchoolTestSubmission
                            ::STATUS_APPROVED
                            ? (
                                $submission
                                    ->access_granted_at
                                ?? now()
                            )
                            : null,
                ]);

                if (
                    $validated['status']
                    === HighSchoolTestSubmission
                        ::STATUS_APPROVED
                ) {
                    $this->grantLearningAccess(
                        $submission
                    );
                }
            }
        );

        return redirect()
            ->route(
                'admin.written-tests.show',
                $submission
            )
            ->with(
                'success',
                'La correction a été enregistrée.'
            );
    }

    public function report(
        HighSchoolTestSubmission $submission
    ) {
        $submission->load([
            'user',
            'subject',
            'level',
            'classRoom',
            'reviewer',
            'appointment',
        ]);

        $pdf = Pdf::loadView(
            'admin.high-school-tests.report',
            compact('submission')
        )->setPaper('a4');

        return $pdf->download(
            'rapport-test-'
            . $submission->id
            . '.pdf'
        );
    }

    private function grantLearningAccess(
        HighSchoolTestSubmission $submission
    ): void {
        $exists = DB::table('class_user')
            ->where(
                'user_id',
                $submission->user_id
            )
            ->where(
                'class_id',
                $submission->class_id
            )
            ->where(
                'subject_id',
                $submission->subject_id
            )
            ->exists();

        if (!$exists) {
            DB::table('class_user')->insert([
                'user_id' =>
                    $submission->user_id,
                'class_id' =>
                    $submission->class_id,
                'subject_id' =>
                    $submission->subject_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /*
         * Compatibilité avec les anciennes parties du projet.
         * class_user reste la source principale.
         */
        DB::table('users')
            ->where(
                'id',
                $submission->user_id
            )
            ->whereNull('class_id')
            ->update([
                'class_id' =>
                    $submission->class_id,
                'updated_at' => now(),
            ]);
    }
}
