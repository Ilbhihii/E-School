<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ne rediriger que les étudiants actifs vers leur tableau de bord
        if(auth()->check() && auth()->user()->role === 'student' && auth()->user()->is_active){
            return redirect()->route('student.dashboard');
        }

        return $next($request);
    }
}
