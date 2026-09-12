<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TestAppointment;
use App\Models\VocalTestSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $appointments = TestAppointment::query()
            ->where('user_id', $user->id)
            ->with(['vocalSubmission.subject', 'vocalSubmission.level'])
            ->latest()
            ->get()
            ->map(fn ($appointment) => [
                'id' => $appointment->id,
                'first_name' => $appointment->first_name,
                'last_name' => $appointment->last_name,
                'phone' => $appointment->phone,
                'email' => $appointment->email,
                'city' => $appointment->city,
                'country' => $appointment->country,
                'type' => $appointment->type,
                'type_label' => $appointment->type_label,
                'status' => $appointment->status,
                'vocal_submission' => $appointment->vocalSubmission ? [
                    'subject' => $appointment->vocalSubmission->subject?->name,
                    'level' => $appointment->vocalSubmission->level?->name,
                ] : null,
                'created_at' => $appointment->created_at,
            ]);

        return response()->json(['success' => true, 'data' => $appointments]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(TestAppointment::getTypes())),
            'vocal_test_submission_id' => 'nullable|integer|exists:vocal_test_submissions,id',
        ]);

        $user = $request->user();
        $vocalSubmission = null;

        if (!empty($validated['vocal_test_submission_id'])) {
            $vocalSubmission = VocalTestSubmission::whereKey($validated['vocal_test_submission_id'])
                ->where('user_id', $user->id)
                ->whereNull('consumed_at')
                ->firstOrFail();
        }

        DB::transaction(function () use ($validated, $vocalSubmission, $user) {
            TestAppointment::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'type' => $validated['type'],
                'status' => TestAppointment::STATUS_PENDING,
                'vocal_test_submission_id' => $vocalSubmission?->id,
            ]);

            $vocalSubmission?->update(['consumed_at' => now()]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous créé avec succès. Nous vous contacterons rapidement.',
        ], 201);
    }

    public function types()
    {
        return response()->json([
            'success' => true,
            'data' => collect(TestAppointment::getTypes())->map(fn ($label, $value) => [
                'value' => $value,
                'label' => $label,
            ])->values(),
        ]);
    }
}
