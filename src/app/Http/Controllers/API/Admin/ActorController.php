<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Actor;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\AdminRequest\Actor\ActorRequest;
use App\Http\Requests\AdminRequest\Actor\CreateActorRequest;
use App\Http\Requests\AdminRequest\Actor\UpdateActorRequest;
use App\Http\Resources\Actor\ActorFilterCollection;
use App\Http\Resources\Actor\ActorDetailResource;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActorController extends Controller
{
    public function index(ActorRequest $request): JsonResponse
    {
        $data = Actor::select()
            ->search($request->search)
            ->filter($request->only(['name']))
            ->sort($request->sort)
            ->paginate($request->per_page ?? config('constants.per_page'));

        return ApiResponse::success(
            new ActorFilterCollection($data),
            config('constants.message.fetch_success')
        )->toJson();
    }

    public function show($id): JsonResponse
    {
        $actor = Actor::where('id', $id)->first();

        if (is_null($actor)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_NOT_FOUND
            )->toJson();
        }

        return ApiResponse::success(
            new ActorDetailResource($actor),
            config('constants.message.fetch_success')
        )->toJson();
    }

    public function store(CreateActorRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $actor = Actor::create($data);

            return ApiResponse::success(
                $actor,
                config('constants.message.create_success')
            )->toJson();
        } catch (\Exception $e) {
            return ApiResponse::error(
                config('constants.message.create_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            )->toJson();
        }
    }

    public function update(UpdateActorRequest $request, $id): JsonResponse
    {
        $actor = Actor::where('id', $id)->first();

        if (is_null($actor)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_NOT_FOUND
            )->toJson();
        }
        try {
            $data = $request->validated();

            $actor->update($data);

            return ApiResponse::success(
                $actor,
                config('constants.message.update_success')
            )->toJson();
        } catch (\Exception $e) {
            return ApiResponse::error(
                config('constants.message.update_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            )->toJson();
        }
    }

    public function destroy($id): JsonResponse
    {
        $actor = Actor::where('id', $id)->first();
        if (is_null($actor)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_BAD_REQUEST
            )->toJson();
        }
        try {
            DB::beginTransaction();
            $actor->delete();
            DB::commit();

            return ApiResponse::success(
                [],
                config('constants.message.delete_success')
            )->toJson();
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();

            return ApiResponse::error(
                config('constants.message.delete_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            )->toJson();
        }
    }
}
