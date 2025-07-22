<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Director;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\AdminRequest\Director\DirectorRequest;
use App\Http\Requests\AdminRequest\Director\CreateDirectorRequest;
use App\Http\Requests\AdminRequest\Director\UpdateDirectorRequest;
use App\Http\Resources\Director\DirectorFilterCollection;
use App\Http\Resources\Director\DirectorDetailResource;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DirectorController extends Controller
{
    public function index(DirectorRequest $request): JsonResponse
    {
        $data = Director::select()
            ->search($request->search)
            ->filter($request->only(['name']))
            ->sort($request->sort)
            ->paginate($request->per_page ?? config('constants.per_page'));

        return ApiResponse::success(
            new DirectorFilterCollection($data),
            config('constants.message.fetch_success')
        )->toJson();
    }

    public function show($id): JsonResponse
    {
        $director = Director::where('id', $id)->first();

        if (is_null($director)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_NOT_FOUND
            )->toJson();
        }

        return ApiResponse::success(
            new DirectorDetailResource($director),
            config('constants.message.fetch_success')
        )->toJson();
    }

    public function store(CreateDirectorRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $director = Director::create($data);

            return ApiResponse::success(
                $director,
                config('constants.message.create_success')
            )->toJson();
        } catch (\Exception $e) {
            return ApiResponse::error(
                config('constants.message.create_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            )->toJson();
        }
    }

    public function update(UpdateDirectorRequest $request, $id): JsonResponse
    {
        $director = Director::where('id', $id)->first();

        if (is_null($director)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_NOT_FOUND
            )->toJson();
        }
        try {
            $data = $request->validated();

            $director->update($data);

            return ApiResponse::success(
                $director,
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
        $director = Director::where('id', $id)->first();
        if (is_null($director)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_BAD_REQUEST
            )->toJson();
        }
        try {
            DB::beginTransaction();
            $director->delete();
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
