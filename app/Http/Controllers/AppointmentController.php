<?php

namespace App\Http\Controllers;

use App\Models\TestAppointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Afficher le formulaire de prise de rendez-vous (public)
     */
    public function create(Request $request)
    {
        $type = $request->query('type', '');
        $user = auth()->user();
        return view('front.appointment', compact('type', 'user'));
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
        ];

        $validated = $request->validate($rules);

        TestAppointment::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'phone'      => $validated['phone'],
            'email'      => $validated['email'],
            'city'       => $validated['city'],
            'country'    => $validated['country'],
            'type'       => $validated['type'],
            'status'     => TestAppointment::STATUS_PENDING,
        ]);

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
        $appointments = TestAppointment::latest()->get();

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
        $appointment->delete();

        return redirect()->back()->with('success', 'Rendez-vous supprimé.');
    }
}
