<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RequireSessionOrAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow if a standard Laravel auth user is logged in
        if (Auth::check()) {
            return $next($request);
        }

        // Allow if legacy session-based login exists (doctor_id or paciente_id)
        if (Session::has('doctor_id') || Session::has('paciente_id')) {
            return $next($request);
        }

        // Otherwise redirect to public inicio
        return redirect()->route('inicio');
    }
}
