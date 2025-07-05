<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return ApiResponse::error('Token not provided', Response::HTTP_UNAUTHORIZED)->toJson();
            }

            $payload = JWTAuth::getPayload($token);

            if (!$payload->get('refresh')) {
                return ApiResponse::error('Invalid token type', Response::HTTP_UNAUTHORIZED)->toJson();
            }

            return $next($request);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage() ?? 'Token is invalid', Response::HTTP_UNAUTHORIZED)->toJson();
        }
    }
}
