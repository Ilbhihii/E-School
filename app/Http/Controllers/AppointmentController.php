<?php

namespace App\Http\Controllers;

use App\Models\HighSchoolTestSubmission;
use App\Models\TestAppointment;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppointmentController extends Controller
{
    /**
     * Afficher le formulaire de prise de rendez-vous.
     */
    public function create(Request $request)
    {
        $type = $request->query('type', '');
        $user = auth()->user();

        $vocalSubmission = null;
        $highSchoolSubmission = null;

        if ($request->filled('vocal_submission')) {
            abort_unless($user, 403);

            $vocalSubmission =
                VocalTestSubmission::with([
                    'subject',
                    'level',
                    'classRoom',
                ])
                ->whereKey(
                    (int) $request->query(
                        'vocal_submission'
                    )
                )
                ->where('user_id', $user->id)
                ->firstOrFail();

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
            abort_unless($user, 403);

            $highSchoolSubmission =
                HighSchoolTestSubmission::with([
                    'subject',
                    'level',
                    'classRoom',
                ])
                ->whereKey(
                    (int) $request->query(
                        'written_submission'
                    )
                )
                ->where('user_id', $user->id)
                ->firstOrFail();

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

        return view(
            'front.appointment',
            compact(
                'type',
                'user',
                'vocalSubmission',
                'highSchoolSubmission'
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
                'required|string|max:20',
            'email' =>
                'required|email|max:255',
            'city' =>
                'required|string|max:255',
            'country' =>
                'required|string|max:255',
            'type' =>
                'required|string|in:'
                . implode(
                    ',',
                    array_keys(
                        TestAppointment::getTypes()
                    )
                ),
            'vocal_test_submission_id' =>
                'nullable|integer|exists:'
                . 'vocal_test_submissions,id',
            'high_school_test_submission_id' =>
                'nullable|integer|exists:'
                . 'high_school_test_submissions,id',
        ]);

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
            abort_unless(auth()->check(), 403);

            $vocalSubmission =
                VocalTestSubmission::query()
                    ->whereKey(
                        $validated[
                            'vocal_test_submission_id'
                        ]
                    )
                    ->where(
                        'user_id',
                        auth()->id()
                    )
                    ->whereNull('consumed_at')
                    ->firstOrFail();

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
            abort_unless(auth()->check(), 403);

            $highSchoolSubmission =
                HighSchoolTestSubmission::query()
                    ->whereKey(
                        $validated[
                            'high_school_test_submission_id'
                        ]
                    )
                    ->where(
                        'user_id',
                        auth()->id()
                    )
                    ->whereNull('consumed_at')
                    ->firstOrFail();

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
                $highSchoolSubmission
            ) {
                $appointment =
                    TestAppointment::create([
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
            $vocalSubmission
            || $highSchoolSubmission
        ) {
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
     * Rendez-vous de tests reçus par l'administration.
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
                        );
                })
                ->with([
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
}
