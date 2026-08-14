<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsParent
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'parent') {
            abort(403, 'Accès réservé aux parents.');
        }

        return $next($request);
    }
}
