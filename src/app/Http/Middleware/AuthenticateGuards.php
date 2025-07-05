<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateGuards
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        try {
            $guards = array_keys(config('auth.guards', []));
            if ($guard && !in_array($guard, $guards)) {
                return ApiResponse::error('Invalid guard', Response::HTTP_UNAUTHORIZED)->toJson();
            }
            $payload = JWTAuth::parseToken()->getPayload();
            $tokenGuard = $payload->get('guard', 'user');
            if ($guard && $tokenGuard !== $guard) {
                return ApiResponse::error('Guard mismatch', Response::HTTP_UNAUTHORIZED)->toJson();
            }
            if ($tokenGuard == 'user' && !$payload->get('email_verified', false)) {
                return ApiResponse::error('Email not verified', Response::HTTP_UNAUTHORIZED)->toJson();
            }
            // TODO: Check if user is banned/disabled/deleted,...
        } catch (JWTException $e) {
            return ApiResponse::error('Invalid or missing token', Response::HTTP_UNAUTHORIZED)->toJson();
        }
        Auth::shouldUse($tokenGuard);

        return $next($request);
    }
}
