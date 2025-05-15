<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

class Suspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user() && $request->user()->isSuspended()
        ) {
            if (auth()->guard()?->name === 'api') {
                $request->user()->currentAccessToken()->delete();

                throw new AuthorizationException('Your account has been suspended for an indefinite period..');
            }

            if (auth()->guard()?->name === 'web') {
                auth()->guard('web')->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect()->route('login')->with([
                    'status' => 'Your account has been suspended for an indefinite period.'
                ]);
            }
        }

        return $next($request);
    }
}
