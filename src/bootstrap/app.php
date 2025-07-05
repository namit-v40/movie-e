<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('auth', [
            \App\Http\Middleware\AuthenticateGuards::class,
        ]);
        $middleware->append(\App\Http\Middleware\ForceJsonRequestHeader::class);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verify.webhook' => \App\Http\Middleware\VerifyWebhookSignature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                $response = [];
                foreach ($exception->errors() as $key => $error) {
                    $response[$key] = head($error);
                }

                return response()->json([
                    'success' => false,
                    'message' => '',
                    'errors' => $response,
                    'data' => null,
                ], $exception->status);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    config('constants.message.permission_denied'),
                    $exception->getStatusCode()
                )->toJson();
            }
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    config('constants.message.not_found'),
                    $exception->getStatusCode()
                )->toJson();
            }
        });
    })->create();
