<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Resources\User\UserDetailResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    const GUARD_USER = 'user';

    const GUARD_ADMIN = 'admin';

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $guard = $request->input('guard', self::GUARD_USER);

        if (!auth($guard)->attempt($credentials)) {
            if ($guard === self::GUARD_ADMIN) {
                return ApiResponse::error(
                    config('constants.message.credentials_do_not_match_record'),
                    Response::HTTP_BAD_REQUEST
                )->toJson();
            }
            if (!$this->checkUser($credentials)) {
                return ApiResponse::error(
                    config('constants.message.credentials_do_not_match_record'),
                    Response::HTTP_BAD_REQUEST
                )->toJson();
            }
        }
        $user = auth($guard)->user();
        
        if (!$user->hasVerifiedEmail() && $guard === self::GUARD_USER) {
            return ApiResponse::error('email_has_not_been_verified', Response::HTTP_BAD_REQUEST)->toJson();
        }
        $token = Auth::guard('user')->login($user);

        return ApiResponse::success(
            $this->respondWithToken($token, $guard, $user),
            config('constants.message.authentication_success')
        )->toJson();
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return ApiResponse::success(
                null,
                config('constants.message.logout_success')
            )->toJson();
        } catch (\Exception $e) {
            return ApiResponse::error(
                config('constants.message.logout_failure'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            )->toJson();
        }
    }

    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $guard = Auth::getDefaultDriver();
            Auth::shouldUse($guard);
            $user = auth($guard)->user();
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return ApiResponse::success(
                $this->respondWithToken($newToken, $guard, $user),
                config('constants.message.token_refresh_success')
            )->toJson();

        } catch (\Exception $e) {
            return ApiResponse::error(
                config('constants.message.token_refresh_failure'),
                Response::HTTP_UNAUTHORIZED
            )->toJson();
        }
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new UserDetailResource(Auth::user()),
            config('constants.message.user_data_retrieved')
        )->toJson();
    }

    protected function checkUser($credentials): bool
    {
        $existingUser = User::where('email', $credentials['email'])->first();
        if (is_null($existingUser)) {
            return false;
        }

        $encrypted_password = $existingUser->password;
        if (str_starts_with($encrypted_password, '$2a$')) {
            $encrypted_password = str_replace('$2a$', '$2y$', $encrypted_password);
        }
        if ($existingUser && Hash::check($credentials['password'], $encrypted_password)) {
            $user = User::updateOrCreate([
                'email' => $credentials['email'],
            ], [
                'password' => bcrypt($credentials['password']),
                'email_verified_at' => $existingUser->email_verified_at,
                'user_identify' => User::generateUserIdentifyByEmail($credentials['email']),
            ]);
            $user = $user->refresh();
            Auth::guard(self::GUARD_USER)->login($user);

            return true;
        }

        return false;
    }
    protected function respondWithToken($token, $guard, $user)
    {
        return [
            'access_token' => $token,
            'refresh_token' => JWTAuth::claims(['refresh' => true])->fromUser($user),
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }
}
