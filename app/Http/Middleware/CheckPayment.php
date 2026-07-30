<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPayment
{
    public function handle(
        Request $request,
        Closure $next
    ) {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin() || $user->isProf()) {
            return $next($request);
        }

        $hasPaidAccess =
            (bool) $user->is_paid
            || (bool) (
                $user->getAttribute('is_subscribed')
                ?? false
            );

        if (!$hasPaidAccess) {
            return redirect()
                ->route('plans')
                ->with(
                    'error',
                    'Un abonnement actif est nécessaire '
                    . 'pour accéder aux lives.'
                );
        }

        return $next($request);
    }
}
