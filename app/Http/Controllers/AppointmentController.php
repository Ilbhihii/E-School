<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentPaymentInvitationMailable;
use App\Models\ClassRoom;
use App\Models\HighSchoolTestSubmission;
use App\Models\Level;
use App\Models\Subject;
use App\Models\TestAppointment;
use App\Models\VocalTestPrompt;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Throwable;

class AppointmentController extends Controller
{
    /**
     * Afficher le formulaire de prise de rendez-vous.
     */
    public function create(Request $request)
    {
        $type = $request->query('type', '');
        $user = auth()->user();
        $submissionToken = trim(
            (string) $request->query(
                'submission_token',
                ''
            )
        );

        $vocalSubmission = null;
        $highSchoolSubmission = null;

        $interviewSubject = null;
        $interviewLevel = null;
        $interviewClass = null;

        $hasAnyInterviewPath = (
            $request->filled('subject_id')
            || $request->filled('level_id')
            || $request->filled('class_id')
        );

        if ($hasAnyInterviewPath) {
            abort_unless(
                $request->filled('subject_id')
                && $request->filled('level_id')
                && $request->filled('class_id'),
                422,
                'Le parcours de l’entretien est incomplet.'
            );

            [
                $interviewSubject,
                $interviewLevel,
                $interviewClass,
            ] = $this->resolveHighSchoolPath(
                (int) $request->query('subject_id'),
                (int) $request->query('level_id'),
                (int) $request->query('class_id')
            );

            $type = TestAppointment::TYPE_TEST;
        }

        if ($request->filled('vocal_submission')) {
            $vocalSubmission =
                $this->resolveVocalSubmission(
                    (int) $request->query(
                        'vocal_submission'
                    ),
                    $submissionToken,
                    false
                );

            if ($vocalSubmission->consumed_at) {
                $appointment =
                    $vocalSubmission->appointment;

                abort_unless($appointment, 404);

                return view(
                    'front.appointment-confirmation',
                    compact(
                        'appointment',
                        'vocalSubmission',
                        'highSchoolSubmission'
                    )
                );
            }

            $type = TestAppointment::TYPE_TEST;
        }

        if ($request->filled('written_submission')) {
            $highSchoolSubmission =
                $this->resolveHighSchoolSubmission(
                    (int) $request->query(
                        'written_submission'
                    ),
                    $submissionToken,
                    false
                );

            if (
                $highSchoolSubmission->consumed_at
            ) {
                $appointment =
                    $highSchoolSubmission->appointment;

                abort_unless($appointment, 404);

                return view(
                    'front.appointment-confirmation',
                    compact(
                        'appointment',
                        'vocalSubmission',
                        'highSchoolSubmission'
                    )
                );
            }

            $type = TestAppointment::TYPE_TEST;
        }

        $interviewMethods =
            TestAppointment::getInterviewMethods();

        return view(
            'front.appointment',
            compact(
                'type',
                'user',
                'vocalSubmission',
                'highSchoolSubmission',
                'interviewSubject',
                'interviewLevel',
                'interviewClass',
                'interviewMethods',
                'submissionToken'
            )
        );
    }

    /**
     * Enregistrer un rendez-vous.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' =>
                'required|string|max:255',
            'last_name' =>
                'required|string|max:255',
            'phone' =>
                'required|string|max:30',
            'email' =>
                'required|email|max:255',
            'city' =>
                'required|string|max:255',
            'country' =>
                'required|string|max:255',
            'type' => [
                'required',
                'string',
                Rule::in(
                    array_keys(
                        TestAppointment::getTypes()
                    )
                ),
            ],
            'vocal_test_submission_id' =>
                'nullable|integer|exists:'
                . 'vocal_test_submissions,id',
            'high_school_test_submission_id' =>
                'nullable|integer|exists:'
                . 'high_school_test_submissions,id',
            'interview_path' =>
                'nullable|boolean',
            'submission_token' =>
                'nullable|string|max:80',
        ]);

        $isDirectInterview =
            $request->boolean('interview_path');

        if ($isDirectInterview) {
            $interviewData =
                $request->validate([
                    'subject_id' =>
                        'required|integer|exists:subjects,id',
                    'level_id' =>
                        'required|integer|exists:levels,id',
                    'class_id' =>
                        'required|integer|exists:class_rooms,id',
                    'interview_method' => [
                        'required',
                        'string',
                        Rule::in(
                            array_keys(
                                TestAppointment
                                    ::getInterviewMethods()
                            )
                        ),
                    ],
                    'preferred_date' =>
                        'required|date|after_or_equal:today',
                    'preferred_time' =>
                        'required|date_format:H:i',
                    'notes' =>
                        'nullable|string|max:1000',
                ]);

            $validated = array_merge(
                $validated,
                $interviewData
            );

            abort_unless(
                $validated['type']
                === TestAppointment::TYPE_TEST,
                422,
                'Le type de rendez-vous est invalide.'
            );

            abort_if(
                !empty(
                    $validated[
                        'vocal_test_submission_id'
                    ]
                )
                || !empty(
                    $validated[
                        'high_school_test_submission_id'
                    ]
                ),
                422,
                'Un entretien direct ne peut pas contenir '
                . 'une autre soumission de test.'
            );

            $this->resolveHighSchoolPath(
                (int) $validated['subject_id'],
                (int) $validated['level_id'],
                (int) $validated['class_id']
            );
        }

        abort_if(
            !empty(
                $validated[
                    'vocal_test_submission_id'
                ]
            )
            && !empty(
                $validated[
                    'high_school_test_submission_id'
                ]
            ),
            422,
            'Un rendez-vous ne peut contenir '
            . 'qu’un seul type de test.'
        );

        $vocalSubmission = null;
        $highSchoolSubmission = null;

        if (
            !empty(
                $validated[
                    'vocal_test_submission_id'
                ]
            )
        ) {
            $vocalSubmission =
                $this->resolveVocalSubmission(
                    (int) $validated[
                        'vocal_test_submission_id'
                    ],
                    (string) (
                        $validated['submission_token']
                        ?? ''
                    ),
                    true
                );

            abort_unless(
                $validated['type']
                === TestAppointment::TYPE_TEST,
                422
            );
        }

        if (
            !empty(
                $validated[
                    'high_school_test_submission_id'
                ]
            )
        ) {
            $highSchoolSubmission =
                $this->resolveHighSchoolSubmission(
                    (int) $validated[
                        'high_school_test_submission_id'
                    ],
                    (string) (
                        $validated['submission_token']
                        ?? ''
                    ),
                    true
                );

            abort_unless(
                $validated['type']
                === TestAppointment::TYPE_TEST,
                422
            );
        }

        $appointment = DB::transaction(
            function () use (
                $validated,
                $vocalSubmission,
                $highSchoolSubmission,
                $isDirectInterview
            ) {
                $appointment =
                    TestAppointment::create([
                        'user_id' => auth()->id(),
                        'first_name' =>
                            $validated['first_name'],
                        'last_name' =>
                            $validated['last_name'],
                        'phone' =>
                            $validated['phone'],
                        'email' =>
                            $validated['email'],
                        'city' =>
                            $validated['city'],
                        'country' =>
                            $validated['country'],
                        'type' =>
                            $validated['type'],
                        'status' =>
                            TestAppointment
                                ::STATUS_PENDING,
                        'subject_id' =>
                            $isDirectInterview
                                ? $validated['subject_id']
                                : (
                                    $vocalSubmission?->subject_id
                                    ?? $highSchoolSubmission?->subject_id
                                ),
                        'level_id' =>
                            $isDirectInterview
                                ? $validated['level_id']
                                : (
                                    $vocalSubmission?->level_id
                                    ?? $highSchoolSubmission?->level_id
                                ),
                        'class_id' =>
                            $isDirectInterview
                                ? $validated['class_id']
                                : (
                                    $vocalSubmission?->class_id
                                    ?? $highSchoolSubmission?->class_id
                                ),
                        'interview_method' =>
                            $isDirectInterview
                                ? $validated[
                                    'interview_method'
                                ]
                                : null,
                        'preferred_date' =>
                            $isDirectInterview
                                ? $validated[
                                    'preferred_date'
                                ]
                                : null,
                        'preferred_time' =>
                            $isDirectInterview
                                ? $validated[
                                    'preferred_time'
                                ]
                                : null,
                        'notes' =>
                            $isDirectInterview
                                ? (
                                    $validated['notes']
                                    ?? null
                                )
                                : null,
                        'vocal_test_submission_id' =>
                            $vocalSubmission?->id,
                        'high_school_test_submission_id' =>
                            $highSchoolSubmission?->id,
                    ]);

                if (auth()->check()) {
                    auth()->user()->update([
                        'city' =>
                            $validated['city'],
                        'country' =>
                            $validated['country'],
                    ]);
                }

                $vocalSubmission?->update([
                    'consumed_at' => now(),
                ]);

                $highSchoolSubmission?->update([
                    'consumed_at' => now(),
                ]);

                return $appointment;
            }
        );

        if (
            auth()->guest()
            && $validated['type']
                === TestAppointment::TYPE_TEST
        ) {
            session()->put(
                'pending_test_registration',
                [
                    'appointment_id' => $appointment->id,
                    'first_name' => $appointment->first_name,
                    'last_name' => $appointment->last_name,
                    'email' => $appointment->email,
                    'city' => $appointment->city,
                    'country' => $appointment->country,
                ]
            );

            session()->forget('url.intended');

            return redirect()
                ->route(
                    'register',
                    ['from' => 'test-appointment']
                )
                ->with(
                    'success',
                    'Votre test et votre rendez-vous ont été envoyés. Créez maintenant votre compte pour terminer l’inscription.'
                );
        }

        if (
            $isDirectInterview
            || $vocalSubmission
            || $highSchoolSubmission
        ) {
            $appointment->load([
                'subject',
                'level',
                'classRoom',
            ]);

            return view(
                'front.appointment-confirmation',
                compact(
                    'appointment',
                    'vocalSubmission',
                    'highSchoolSubmission'
                )
            );
        }

        $redirect = $request->query(
            'redirect',
            'back'
        );

        if (
            $redirect === 'student.waiting'
            && auth()->check()
        ) {
            return redirect()
                ->route('student.waiting')
                ->with(
                    'success',
                    'Votre demande de rendez-vous '
                    . 'a été envoyée avec succès.'
                );
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Votre demande de rendez-vous '
                . 'a été envoyée avec succès.'
            );
    }

    /**
     * Rendez-vous reçus par l'administration.
     */
    public function index()
    {
        $appointments =
            TestAppointment::query()
                ->where(
                    'type',
                    TestAppointment::TYPE_TEST
                )
                ->where(function ($query) {
                    $query
                        ->whereNotNull(
                            'vocal_test_submission_id'
                        )
                        ->orWhereNotNull(
                            'high_school_test_submission_id'
                        )
                        ->orWhereNotNull(
                            'subject_id'
                        );
                })
                ->with([
                    'user',
                    'subject',
                    'level',
                    'classRoom',
                    'vocalSubmission.subject',
                    'vocalSubmission.level',
                    'vocalSubmission.classRoom',
                    'highSchoolTestSubmission.subject',
                    'highSchoolTestSubmission.level',
                    'highSchoolTestSubmission.classRoom',
                ])
                ->latest()
                ->get();

        return view(
            'admin.appointments.index',
            compact('appointments')
        );
    }

    public function confirm(
        TestAppointment $appointment
    ) {
        $appointment->update([
            'status' =>
                TestAppointment::STATUS_CONFIRMED,
        ]);

        return back()->with(
            'success',
            'Rendez-vous confirmé.'
        );
    }

    public function cancel(
        TestAppointment $appointment
    ) {
        $appointment->update([
            'status' =>
                TestAppointment::STATUS_CANCELLED,
        ]);

        return back()->with(
            'success',
            'Rendez-vous annulé.'
        );
    }

    public function sendPaymentEmail(
        TestAppointment $appointment
    ) {
        $appointment->load([
            'user',
            'subject',
            'level',
            'classRoom',
            'vocalSubmission.subject',
            'vocalSubmission.level',
            'vocalSubmission.classRoom',
            'highSchoolTestSubmission.subject',
            'highSchoolTestSubmission.level',
            'highSchoolTestSubmission.classRoom',
        ]);

        if (!$appointment->canReceivePaymentInvitation()) {
            return back()->with(
                'error',
                'Confirmez d’abord le rendez-vous '
                . 'et vérifiez l’adresse e-mail.'
            );
        }

        $planCode = $appointment->payment_plan_code;
        $plan = $appointment->payment_plan_details;
        $paymentUrl = $this->temporaryPaymentUrl($appointment);

        try {
            Mail::to($appointment->email)->send(
                new AppointmentPaymentInvitationMailable(
                    $appointment,
                    $paymentUrl,
                    $plan
                )
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'L’e-mail n’a pas pu être envoyé. '
                . 'Vérifiez la configuration MAIL du serveur.'
            );
        }

        $appointment->forceFill([
            'payment_plan' => $planCode,
            'payment_invited_at' => now(),
            'payment_invitation_count' =>
                (int) $appointment->payment_invitation_count + 1,
        ])->save();

        return back()->with(
            'success',
            'L’e-mail de paiement a été envoyé à '
            . $appointment->email
            . '.'
        );
    }

    public function paymentInvitation(
        Request $request,
        TestAppointment $appointment
    ) {
        abort_unless(
            $request->hasValidSignature(),
            403,
            'Le lien de paiement est invalide ou a expiré.'
        );

        abort_unless(
            $appointment->status === TestAppointment::STATUS_CONFIRMED,
            403,
            'Ce rendez-vous n’est pas confirmé.'
        );

        return redirect()->route(
            'student.payment',
            [
                'plan' => $appointment->payment_plan_code,
                'appointment' => $appointment->id,
            ]
        );
    }

    private function temporaryPaymentUrl(
        TestAppointment $appointment
    ): string {
        return URL::temporarySignedRoute(
            'appointment.payment',
            now()->addDays(7),
            [
                'appointment' => $appointment->id,
            ]
        );
    }

    public function destroy(
        TestAppointment $appointment
    ) {
        $vocalSubmission =
            $appointment->vocalSubmission;

        $highSchoolSubmission =
            $appointment->highSchoolTestSubmission;

        $appointment->delete();

        if ($vocalSubmission) {
            Storage::disk('local')->delete(
                $vocalSubmission->audio_path
            );

            $vocalSubmission->delete();
        }

        if ($highSchoolSubmission) {
            foreach (
                $highSchoolSubmission->images()
                as $image
            ) {
                if (!empty($image['path'])) {
                    Storage::disk('local')->delete(
                        $image['path']
                    );
                }
            }

            $highSchoolSubmission->delete();
        }

        return back()->with(
            'success',
            'Rendez-vous supprimé.'
        );
    }

    public function audio(
        TestAppointment $appointment
    ) {
        $submission =
            $appointment->vocalSubmission;

        abort_unless(
            $submission
            && Storage::disk('local')->exists(
                $submission->audio_path
            ),
            404
        );

        return response()->file(
            Storage::disk('local')->path(
                $submission->audio_path
            ),
            [
                'Content-Type' =>
                    $submission->audio_mime_type
                    ?: 'audio/webm',
                'Content-Disposition' =>
                    'inline; filename="recitation-'
                    . $appointment->id
                    . '.webm"',
                'Cache-Control' =>
                    'private, no-store',
            ]
        );
    }


    private function resolveVocalSubmission(
        int $submissionId,
        string $submissionToken,
        bool $mustBeUnconsumed
    ): VocalTestSubmission {
        $query = VocalTestSubmission::with([
            'subject',
            'level',
            'classRoom',
        ])->whereKey($submissionId);

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            abort_if(
                $submissionToken === '',
                403,
                'Le lien du test est invalide.'
            );

            $query
                ->whereNull('user_id')
                ->where(
                    'guest_token',
                    $submissionToken
                );
        }

        if ($mustBeUnconsumed) {
            $query->whereNull('consumed_at');
        }

        return $query->firstOrFail();
    }

    private function resolveHighSchoolSubmission(
        int $submissionId,
        string $submissionToken,
        bool $mustBeUnconsumed
    ): HighSchoolTestSubmission {
        $query = HighSchoolTestSubmission::with([
            'subject',
            'level',
            'classRoom',
        ])->whereKey($submissionId);

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            abort_if(
                $submissionToken === '',
                403,
                'Le lien du test est invalide.'
            );

            $query
                ->whereNull('user_id')
                ->where(
                    'guest_token',
                    $submissionToken
                );
        }

        if ($mustBeUnconsumed) {
            $query->whereNull('consumed_at');
        }

        return $query->firstOrFail();
    }

    private function resolveHighSchoolPath(
        int $subjectId,
        int $levelId,
        int $classId
    ): array {
        $subject =
            Subject::findOrFail($subjectId);

        abort_unless(
            VocalTestPrompt::normalizePathName(
                $subject->name
            ) === 'soutien lycee',
            404
        );

        $level = Level::query()
            ->whereKey($levelId)
            ->where(
                'subject_id',
                $subject->id
            )
            ->firstOrFail();

        $class = ClassRoom::query()
            ->whereKey($classId)
            ->where(
                'level_id',
                $level->id
            )
            ->whereHas(
                'subjects',
                function ($query) use ($subject) {
                    $query->where(
                        'subjects.id',
                        $subject->id
                    );
                }
            )
            ->firstOrFail();

        return [
            $subject,
            $level,
            $class,
        ];
    }
}
