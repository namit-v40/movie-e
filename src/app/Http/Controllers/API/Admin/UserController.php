<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserDetailWithCreatorResource;
use App\Http\Resources\Admin\UserFilterCollection;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::filter($request->all())
            ->detail()
            ->sort($request->get('sort', '-created_at'))
            ->paginate($request->per_page ?? config('constants.per_page'));

        return ApiResponse::success(
            new UserFilterCollection($users),
            config('constants.message.get_list_success')
        )->toJson();
    }

    public function show($id)
    {
        $user = User::select()->detail()->where('id', $id)->first();

        if (!$user) {
            return ApiResponse::error(config('constants.message.not_found'), Response::HTTP_NOT_FOUND)->toJson();
        }

        return ApiResponse::success(new UserDetailWithCreatorResource($user), config('constants.message.fetch_success'))->toJson();
    }
}
