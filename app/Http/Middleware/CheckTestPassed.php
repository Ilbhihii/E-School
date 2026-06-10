<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTestPassed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user?->hasRole('student') || !$user->test_passed) {
            return redirect()->route('student.tests.index')
                ->with('error', 'Vous devez passer le test d\'abord');
        }

        return $next($request);
    }
}

