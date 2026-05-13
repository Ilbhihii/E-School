<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Si abonné → accès autorisé
        if ($user->is_subscribed) {
            return $next($request);
        }

        // Si encore en période gratuite
        if ($user->trial_ends_at && Carbon::now()->lessThanOrEqualTo($user->trial_ends_at)) {
            return $next($request);
        }

        // Sinon redirection vers offres
        return redirect()->route('plans')
            ->with('error', 'Votre période gratuite est terminée. Veuillez vous abonner.');
    }

}
