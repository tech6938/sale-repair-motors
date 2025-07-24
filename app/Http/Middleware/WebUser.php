<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class WebUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user() && !$request->user()->isSuperAdmin() && !$request->user()->isAdmin() && !$request->user()->isPreparationManager()
        ) {
            auth()->guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        return $next($request);
    }
}
