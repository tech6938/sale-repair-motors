<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user() && $request->user()->isSuperAdmin()
        ) {
            $request->user()->currentAccessToken()->delete();

            throw new \Exception('We cannot find the email address in our system.', JsonResponse::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
