<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class FirstPasswordController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        $isExpired =
            $this->temporaryPasswordExpired(
                $user
            );

        return view(
            'prof.first-password',
            compact('isExpired')
        );
    }

    public function update(Request $request)
    {
        $user = $request->user();

        if (
            $this->temporaryPasswordExpired(
                $user
            )
        ) {
            return back()->withErrors([
                'password' =>
                    'Le mot de passe temporaire '
                    . 'a expiré. Demandez à '
                    . 'l’administration de renvoyer '
                    . 'vos accès.',
            ]);
        }

        $validated = $request->validate(
            [
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(10)
                        ->letters()
                        ->mixedCase()
                        ->numbers(),
                ],
            ],
            [
                'password.required' =>
                    'Le nouveau mot de passe '
                    . 'est obligatoire.',
                'password.confirmed' =>
                    'La confirmation ne correspond pas.',
            ]
        );

        $user->forceFill([
            'password' => Hash::make(
                $validated['password']
            ),
            'must_change_password' => false,
            'temporary_password_expires_at' =>
                null,
            'password_changed_at' => now(),
        ])->save();

        return redirect()
            ->route('prof.dashboard')
            ->with(
                'success',
                'Votre nouveau mot de passe '
                . 'a été enregistré.'
            );
    }

    private function temporaryPasswordExpired(
        $user
    ): bool {
        $expiresAt = $user->getAttribute(
            'temporary_password_expires_at'
        );

        if (!$expiresAt) {
            return false;
        }

        return now()->greaterThan(
            Carbon::parse($expiresAt)
        );
    }
}
