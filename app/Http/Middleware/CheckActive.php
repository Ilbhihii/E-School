<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->is_active) {
            if (Auth::user()->hasRole('student')) {
                return redirect()->route('student.waiting')
                    ->with('info', 'Votre compte est en attente d\'activation par un administrateur.');
            }
            return redirect()->route('account.blocked')
                ->with('info', 'Votre compte est en attente d\'activation par un administrateur.');
        }

        return $next($request);
    }
}
