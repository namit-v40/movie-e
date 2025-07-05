<?php

namespace App\Http\Controllers\API\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string|email|max:255|unique:users|regex:/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'email.unique' => 'This email address is already in use.',
            ]
        );

        $user = User::where('email', $request->email)->first();
        if ($user) {
            return ApiResponse::error('email_already_exists', Response::HTTP_BAD_REQUEST)->toJson();
        }

        $encrypted_password = Hash::make($request->password);

        $userIdentify = User::generateUserIdentifyByEmail($request->email);

        $user = User::create([
            'role' => User::ROLE_USER,
            'email' => $request->email,
            'password' => $encrypted_password,
            'user_identify' => $userIdentify,
        ]);

        $token = Password::createToken($user);

        $verificationUrl = config('app.client_url') . "/email-verification?token={$token}&email={$user->email}";

        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

        return ApiResponse::success(
            [],
            'user_registered_successfully_check_your_email_for_verification',
            Response::HTTP_CREATED
        )->toJson();
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'token' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ApiResponse::error('user_not_found', Response::HTTP_NOT_FOUND)->toJson();
        }

        $exited = Password::tokenExists($user, $request->token);

        if (!$exited) {
            return ApiResponse::error('invalid_or_expired_token', Response::HTTP_BAD_REQUEST)->toJson();
        }
        if ($user->hasVerifiedEmail()) {
            return ApiResponse::error('email_already_verified', Response::HTTP_BAD_REQUEST)->toJson();
        }

        $user->markEmailAsVerified();

        return ApiResponse::success([], 'email_successfully_verified')->toJson();
    }

    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ApiResponse::error('user_not_found', Response::HTTP_NOT_FOUND)->toJson();
        }
        if ($user->hasVerifiedEmail()) {
            return ApiResponse::error('email_already_verified', Response::HTTP_BAD_REQUEST)->toJson();
        }

        $token = Password::createToken($user);

        $verificationUrl = config('app.client_url') . "/email-verification?token={$token}&email={$user->email}";

        Mail::to($user->email)->queue(new VerifyEmail($user, $verificationUrl));

        return ApiResponse::success([], 'email_verification_link_resent_successfully', Response::HTTP_CREATED)->toJson();
    }
}
