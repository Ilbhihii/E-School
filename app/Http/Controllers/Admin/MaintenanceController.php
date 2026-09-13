<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSetting;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function edit()
    {
        $maintenance = MaintenanceSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'announcement_enabled' => false,
                'blocking_enabled' => false,
                'message' =>
                    'Une maintenance de la plateforme est programmée.',
            ]
        );

        return view(
            'admin.maintenance.edit',
            compact('maintenance')
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'max:500',
            ],
            'start_at' => [
                'required',
                'date',
            ],
            'end_at' => [
                'required',
                'date',
                'after:start_at',
            ],
        ], [
            'end_at.after' =>
                'La fin de la maintenance doit être postérieure au début.',
        ]);

        $maintenance = MaintenanceSetting::query()->firstOrCreate([
            'id' => 1,
        ]);

        $maintenance->update([
            'announcement_enabled' =>
                $request->boolean('announcement_enabled'),
            'blocking_enabled' =>
                $request->boolean('blocking_enabled'),
            'message' =>
                $validated['message'],
            'start_at' =>
                $validated['start_at'],
            'end_at' =>
                $validated['end_at'],
        ]);

        return back()->with(
            'success',
            'La maintenance a été enregistrée avec succès.'
        );
    }
}
