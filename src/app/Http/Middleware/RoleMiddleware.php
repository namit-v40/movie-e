<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            $tokenRole = $payload->get('role');
            $userId = $payload->get('sub');

            // If token role is valid, allow access
            if (in_array($tokenRole, $roles)) {
                return $next($request);
            }

            // If token guard is user, get user from database and check role
            $tokenGuard = $payload->get('guard', 'user');

            if ($tokenGuard === 'user') {
                $user = User::find($userId);
                if ($user && in_array($user->role, $roles)) {
                    return $next($request);
                }
            }

            return ApiResponse::error('Unauthorized', Response::HTTP_FORBIDDEN)->toJson();
        } catch (\Exception $e) {
            return ApiResponse::error('Unauthorized', Response::HTTP_UNAUTHORIZED)->toJson();
        }
    }
}
