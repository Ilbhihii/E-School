<?php

namespace App\Http\Controllers;

use App\Models\TestAppointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Afficher le formulaire de prise de rendez-vous (public)
     */
    public function create()
    {
        return view('front.appointment');
    }

    /**
     * Enregistrer un rendez-vous (public)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'email'      => 'required|email|max:255',
        ]);

        TestAppointment::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'phone'      => $validated['phone'],
            'email'      => $validated['email'],
            'status'     => TestAppointment::STATUS_PENDING,
        ]);

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
