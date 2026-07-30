<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        return view('auth.register');
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'is_active' => false,
            'country' => $country !== '' ? $country : null,
            'city' => $city !== '' ? $city : null,
            'ip_address' => $ip,
        ]);


        event(new Registered($user));

        Auth::login($user);

        // Reprendre le parcours choisi avant l'inscription (par exemple le
        // test vocal Coran), sinon ouvrir le rendez-vous de test standard.
        return redirect()->intended(route('appointment.create', ['type' => 'test', 'from' => 'registration']));
    }
}
