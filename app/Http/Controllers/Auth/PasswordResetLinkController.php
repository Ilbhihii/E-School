<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        if (config('mail.default') === 'log') {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Le serveur utilise encore le mode de test. Redémarrez Laravel après avoir configuré Gmail.',
            ]);
        }

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (\Throwable $exception) {
            \Log::error('Password reset email delivery failed', [
                'email' => $request->email,
                'error' => $exception->getMessage(),
            ]);

            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'L’email n’a pas pu être envoyé. Vérifiez le compte Gmail et son mot de passe d’application.',
            ]);
        }

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
