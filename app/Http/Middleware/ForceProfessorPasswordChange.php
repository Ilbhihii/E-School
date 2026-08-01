<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceProfessorPasswordChange
{
    public function handle(
        Request $request,
        Closure $next
    ) {
        $user = $request->user();

        if (
            !$user
            || !$user->isProf()
            || !(bool) $user
                ->must_change_password
        ) {
            return $next($request);
        }

        if (
            $request->routeIs(
                'prof.password.first.edit',
                'prof.password.first.update',
                'logout'
            )
        ) {
            return $next($request);
        }

        return redirect()
            ->route(
                'prof.password.first.edit'
            )
            ->with(
                'warning',
                'Vous devez remplacer votre '
                . 'mot de passe temporaire '
                . 'avant de continuer.'
            );
    }
}
