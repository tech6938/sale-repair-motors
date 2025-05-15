<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty(config('api.key'))) {
            throw new \Exception('API key is not configured.', JsonResponse::HTTP_FORBIDDEN);
        }

        if (empty($request->get('token'))) {
            throw new \Exception('API key is not provided.', JsonResponse::HTTP_FORBIDDEN);
        }

        if ($request->get('token') !== config('api.key')) {
            throw new \Exception('Provided API key is invalid.', JsonResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
