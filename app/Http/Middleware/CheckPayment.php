<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPayment
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin() || $user->isProf()) {
            return $next($request);
        }

        if (!$user->isStudent() || !$user->hasCurrentPaidAccess()) {
            return redirect()
                ->route('plans')
                ->with('error', 'Un abonnement actuellement valide est nécessaire pour accéder à ce contenu.');
        }

        return $next($request);
    }
}
