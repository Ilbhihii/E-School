<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckStudentAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // ❌ pas encore passé le test
        if (!$user->test_passed) {
            return redirect()->route('student.tests.index');
        }

        // ⏳ test passé mais pas activé
        if (!$user->is_active) {
            return redirect()->route('student.waiting');
        }

        return $next($request);
    }
}

