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
        ]);

        $ip = request()->ip();

        $position = Location::get($ip);

        $country = $position ? $position->countryName : null;
        $city = $position ? $position->cityName : null;

$user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'student',
        'is_active' => false,
        'country' => $country,
        'city' => $city,
        'ip_address' => $ip
        ]);


        event(new Registered($user));

        Auth::login($user);

        // Reprendre le parcours choisi avant l'inscription (par exemple le
        // test vocal Coran), sinon ouvrir le rendez-vous de test standard.
        return redirect()->intended(route('appointment.create', ['type' => 'test', 'from' => 'registration']));
    }
}
