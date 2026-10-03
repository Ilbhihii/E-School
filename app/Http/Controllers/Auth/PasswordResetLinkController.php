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

        $genericMessage =
            'Si un compte correspond à cette adresse, '
            . 'un lien de réinitialisation sera envoyé.';

        try {
            /*
             * Ne jamais révéler au visiteur si l'adresse existe ni les
             * détails de la configuration du serveur de messagerie.
             */
            if (config('mail.default') === 'log') {
                \Log::warning(
                    'Password reset requested while mailer is set to log.'
                );
            } else {
                Password::sendResetLink(
                    $request->only('email')
                );
            }
        } catch (\Throwable $exception) {
            \Log::error(
                'Password reset email delivery failed',
                [
                    'error' => $exception->getMessage(),
                ]
            );
        }

        return back()->with(
            'status',
            $genericMessage
        );
    }
}
