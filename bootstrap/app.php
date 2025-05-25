<?php

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'suspended' => \App\Http\Middleware\Suspended::class,
        ]);

        $middleware->web(
            append: [
                \App\Http\Middleware\WebUser::class,
            ]
        );

        $middleware->api(
            prepend: [
                \App\Http\Middleware\ApiKey::class,
            ],
            append: [
                \App\Http\Middleware\ApiUser::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found.',
                    'data' => null
                ], JsonResponse::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'data' => null
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->is('api*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => null
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }

            return redirect()->back()->with([
                'status' => $e->getMessage()
            ]);
        });

        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->is('api*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'data' => null
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }

            return redirect()->route('profile.index');
        });

        $exceptions->render(function (\Exception $e, Request $request) {
            if ($request->is('api*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => null
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        });
    })->create();
