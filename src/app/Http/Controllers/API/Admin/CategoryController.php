<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Category;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\AdminRequest\Category\CategoryRequest;
use App\Http\Requests\AdminRequest\Category\CreateCategoryRequest;
use App\Http\Requests\AdminRequest\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryFilterCollection;
use App\Http\Resources\Category\CategoryDetailResource;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(CategoryRequest $request): JsonResponse
    {
        $data = Category::select()
            ->search($request->search)
            ->filter($request->only(['name', 'slug']))
            ->sort($request->sort)
            ->paginate($request->per_page ?? config('constants.per_page'));

        return ApiResponse::success(
            new CategoryFilterCollection($data),
            config('constants.message.fetch_success')
        )->toJson();
    }

    public function show($id): JsonResponse
    {
        $category = Category::where('id', $id)->first();

        if (is_null($category)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_NOT_FOUND
            )->toJson();
        }

        return ApiResponse::success(
            new CategoryDetailResource($category),
            config('constants.message.fetch_success')
        )->toJson();
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $category = Category::create($data);

            return ApiResponse::success(
                $category,
                config('constants.message.create_success')
            )->toJson();
        } catch (\Exception $e) {
            return ApiResponse::error(
                config('constants.message.create_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            )->toJson();
        }
    }

    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        $category = Category::where('id', $id)->first();

        if (is_null($category)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_NOT_FOUND
            )->toJson();
        }
        try {
            $data = $request->validated();

            $category->update($data);

            return ApiResponse::success(
                $category,
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
        $category = Category::where('id', $id)->first();
        if (is_null($category)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_BAD_REQUEST
            )->toJson();
        }
        try {
            DB::beginTransaction();
            $category->delete();
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
