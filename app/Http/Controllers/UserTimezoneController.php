<?php

namespace App\Http\Controllers;

use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserTimezoneController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $validated = $request->validate([
            'timezone' => [
                'required',
                'string',
                'max:64',
                Rule::in(
                    DateTimeZone::listIdentifiers(
                        DateTimeZone::ALL_WITH_BC
                    )
                ),
            ],
        ]);

        if (
            $user->getAttribute('timezone')
            !== $validated['timezone']
        ) {
            $user->forceFill([
                'timezone' => $validated['timezone'],
            ])->save();
        }

        return response()->json([
            'ok' => true,
            'timezone' => $user->effectiveTimezone(),
        ]);
    }
}