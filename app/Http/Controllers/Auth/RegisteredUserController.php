<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TestAppointment;
use App\Models\VocalTestSubmission;
use App\Models\HighSchoolTestSubmission;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Stevebauman\Location\Facades\Location;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $pendingRegistration = session(
            'pending_test_registration'
        );

        $registrationPrefill = [
            'name' => $pendingRegistration
                ? trim(
                    ($pendingRegistration['first_name'] ?? '')
                    . ' '
                    . ($pendingRegistration['last_name'] ?? '')
                )
                : '',
            'email' => $pendingRegistration['email']
                ?? '',
            'country' => $pendingRegistration['country']
                ?? '',
            'city' => $pendingRegistration['city']
                ?? '',
        ];

        return view(
            'auth.register',
            compact(
                'pendingRegistration',
                'registrationPrefill'
            )
        );
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        $ip = $request->ip();

        $country = trim((string) $request->input('country'));
        $city = trim((string) $request->input('city'));

        /*
         * La géolocalisation IP sert uniquement de secours.
         * Sur localhost (127.0.0.1), elle ne retourne normalement rien.
         */
        if ($country === '' || $city === '') {
            try {
                $position = Location::get($ip);

                if ($position) {
                    $country = $country !== ''
                        ? $country
                        : (string) $position->countryName;

                    $city = $city !== ''
                        ? $city
                        : (string) $position->cityName;
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        $pendingRegistration = session(
            'pending_test_registration'
        );

        $user = DB::transaction(
            function () use (
                $request,
                $country,
                $city,
                $ip,
                $pendingRegistration
            ) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make(
                        $request->password
                    ),
                    'role' => 'student',
                    'is_active' => false,
                    'country' => $country !== ''
                        ? $country
                        : null,
                    'city' => $city !== ''
                        ? $city
                        : null,
                    'ip_address' => $ip,
                ]);

                if (
                    $pendingRegistration
                    && !empty(
                        $pendingRegistration[
                            'appointment_id'
                        ]
                    )
                ) {
                    $appointment =
                        TestAppointment::query()
                            ->whereKey(
                                $pendingRegistration[
                                    'appointment_id'
                                ]
                            )
                            ->whereNull('user_id')
                            ->lockForUpdate()
                            ->first();

                    if ($appointment) {
                        $appointment->update([
                            'user_id' => $user->id,
                        ]);

                        if (
                            $appointment
                                ->vocal_test_submission_id
                        ) {
                            VocalTestSubmission::query()
                                ->whereKey(
                                    $appointment
                                        ->vocal_test_submission_id
                                )
                                ->whereNull('user_id')
                                ->update([
                                    'user_id' => $user->id,
                                    'guest_token' => null,
                                ]);
                        }

                        if (
                            $appointment
                                ->high_school_test_submission_id
                        ) {
                            HighSchoolTestSubmission::query()
                                ->whereKey(
                                    $appointment
                                        ->high_school_test_submission_id
                                )
                                ->whereNull('user_id')
                                ->update([
                                    'user_id' => $user->id,
                                    'guest_token' => null,
                                ]);
                        }
                    }
                }

                return $user;
            }
        );

        event(new Registered($user));

        Auth::login($user);

        if ($pendingRegistration) {
            session()->forget(
                'pending_test_registration'
            );

            session()->forget('url.intended');

            return redirect()
                ->route('student.waiting')
                ->with(
                    'success',
                    'Votre compte a été créé et lié à votre test et à votre rendez-vous.'
                );
        }

        // Parcours normal d’inscription hors test d’admission.
        return redirect()->intended(
            route(
                'appointment.create',
                [
                    'type' => 'test',
                    'from' => 'registration',
                ]
            )
        );
    }
}
