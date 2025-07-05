<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserDetailResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function profile()
    {
        $user = User::select()->detail()->where('id', Auth::id())->first();

        if (!$user) {
            return ApiResponse::error(config('constants.message.not_found'), Response::HTTP_NOT_FOUND)->toJson();
        }

        return ApiResponse::success(new UserDetailResource($user), config('constants.message.fetch_success'))->toJson();
    }

    public function show($id)
    {
        $user = User::select()
            ->detail()
            ->where('user_identify', $id)
            ->first();

        if (!$user) {
            return ApiResponse::error(config('constants.message.not_found'), Response::HTTP_NOT_FOUND)->toJson();
        }

        return ApiResponse::success(new UserDetailResource($user), config('constants.message.fetch_success'))->toJson();
    }

    public function getUserById($id)
    {
        $user = User::select()
            ->detail()
            ->where('id', $id)
            ->first();

        if (!$user) {
            return ApiResponse::error(config('constants.message.not_found'), Response::HTTP_NOT_FOUND)->toJson();
        }

        return ApiResponse::success(new UserDetailResource($user), config('constants.message.fetch_success'))->toJson();
    }
}
