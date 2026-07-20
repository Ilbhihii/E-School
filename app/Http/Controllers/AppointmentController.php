<?php

namespace App\Http\Controllers;

use App\Models\TestAppointment;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppointmentController extends Controller
{
    /**
     * Afficher le formulaire de prise de rendez-vous (public)
     */
    public function create(Request $request)
    {
        $type = $request->query('type', '');
        $user = auth()->user();
        $vocalSubmission = null;

        if ($request->filled('vocal_submission')) {
            abort_unless($user, 403);
            $vocalSubmission = VocalTestSubmission::with(['subject', 'level', 'classRoom'])
                ->whereKey((int) $request->query('vocal_submission'))
                ->where('user_id', $user->id)
                ->firstOrFail();

            if ($vocalSubmission->consumed_at) {
                $appointment = $vocalSubmission->appointment;
                abort_unless($appointment, 404);

                return view('front.appointment-confirmation', compact('appointment', 'vocalSubmission'));
            }

            $type = TestAppointment::TYPE_TEST;
        }

        return view('front.appointment', compact('type', 'user', 'vocalSubmission'));
    }

    /**
     * Enregistrer un rendez-vous (public)
     */
    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'email'      => 'required|email|max:255',
            'city'       => 'required|string|max:255',
            'country'    => 'required|string|max:255',
            'type'       => 'required|string|in:' . implode(',', array_keys(TestAppointment::getTypes())),
            'vocal_test_submission_id' => 'nullable|integer|exists:vocal_test_submissions,id',
        ];

        $validated = $request->validate($rules);

        $vocalSubmission = null;
        if (!empty($validated['vocal_test_submission_id'])) {
            abort_unless(auth()->check(), 403);
            $vocalSubmission = VocalTestSubmission::whereKey($validated['vocal_test_submission_id'])
                ->where('user_id', auth()->id())
                ->whereNull('consumed_at')
                ->firstOrFail();
            abort_unless($validated['type'] === TestAppointment::TYPE_TEST, 422);
        }

        DB::transaction(function () use ($validated, $vocalSubmission) {
            TestAppointment::create([
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'phone'      => $validated['phone'],
                'email'      => $validated['email'],
                'city'       => $validated['city'],
                'country'    => $validated['country'],
                'type'       => $validated['type'],
                'status'     => TestAppointment::STATUS_PENDING,
                'vocal_test_submission_id' => $vocalSubmission?->id,
            ]);

            $vocalSubmission?->update(['consumed_at' => now()]);
        });

        $redirect = $request->query('redirect', 'back');

        if ($redirect === 'student.waiting' && auth()->check()) {
            return redirect()->route('student.waiting')->with('success', '✅ Votre demande de rendez-vous a été envoyée avec succès ! Nous vous contacterons rapidement pour fixer la date.');
        }

        return redirect()->back()->with('success', '✅ Votre demande de rendez-vous a été envoyée avec succès ! Nous vous contacterons rapidement.');
    }

    /**
     * Lister tous les rendez-vous (admin)
     */
    public function index()
    {
        $appointments = TestAppointment::where('type', TestAppointment::TYPE_TEST)
            ->whereNotNull('vocal_test_submission_id')
            ->with(['vocalSubmission.subject', 'vocalSubmission.level', 'vocalSubmission.classRoom'])
            ->latest()
            ->get();

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Confirmer un rendez-vous (admin)
     */
    public function confirm(TestAppointment $appointment)
    {
        $appointment->update(['status' => TestAppointment::STATUS_CONFIRMED]);

        return redirect()->back()->with('success', 'Rendez-vous confirmé.');
    }

    /**
     * Annuler un rendez-vous (admin)
     */
    public function cancel(TestAppointment $appointment)
    {
        $appointment->update(['status' => TestAppointment::STATUS_CANCELLED]);

        return redirect()->back()->with('success', 'Rendez-vous annulé.');
    }

    /**
     * Supprimer un rendez-vous (admin)
     */
    public function destroy(TestAppointment $appointment)
    {
        $submission = $appointment->vocalSubmission;
        $appointment->delete();

        if ($submission) {
            Storage::disk('local')->delete($submission->audio_path);
            $submission->delete();
        }

        return redirect()->back()->with('success', 'Rendez-vous supprimé.');
    }

    public function audio(TestAppointment $appointment)
    {
        $submission = $appointment->vocalSubmission;
        abort_unless($submission && Storage::disk('local')->exists($submission->audio_path), 404);

        return response()->file(
            Storage::disk('local')->path($submission->audio_path),
            [
                'Content-Type' => $submission->audio_mime_type ?: 'audio/webm',
                'Content-Disposition' => 'inline; filename="recitation-' . $appointment->id . '.webm"',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }
}
