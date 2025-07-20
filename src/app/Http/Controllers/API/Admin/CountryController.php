<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Country;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\AdminRequest\Country\CountryRequest;
use App\Http\Requests\AdminRequest\Country\CreateCountryRequest;
use App\Http\Requests\AdminRequest\Country\UpdateCountryRequest;
use App\Http\Resources\Country\CountryFilterCollection;
use App\Http\Resources\Country\CountryDetailResource;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function index(CountryRequest $request): JsonResponse
    {
        $data = Country::select()
            ->search($request->search)
            ->filter($request->only(['name', 'code']))
            ->sort($request->sort)
            ->paginate($request->per_page ?? config('constants.per_page'));

        return ApiResponse::success(
            new CountryFilterCollection($data),
            config('constants.message.fetch_success')
        )->toJson();
    }

    public function show($id): JsonResponse
    {
        $country = Country::where('id', $id)->first();

        if (is_null($country)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_NOT_FOUND
            )->toJson();
        }

        return ApiResponse::success(
            new CountryDetailResource($country),
            config('constants.message.fetch_success')
        )->toJson();
    }

    public function store(CreateCountryRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $country = Country::create($data);

            return ApiResponse::success(
                $country,
                config('constants.message.create_success')
            )->toJson();
        } catch (\Exception $e) {
            return ApiResponse::error(
                config('constants.message.create_failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            )->toJson();
        }
    }

    public function update(UpdateCountryRequest $request, $id): JsonResponse
    {
        $country = Country::where('id', $id)->first();

        if (is_null($country)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_NOT_FOUND
            )->toJson();
        }
        try {
            $data = $request->validated();

            $country->update($data);

            return ApiResponse::success(
                $country,
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
        $country = Country::where('id', $id)->first();
        if (is_null($country)) {
            return ApiResponse::error(
                config('constants.message.not_found'),
                Response::HTTP_BAD_REQUEST
            )->toJson();
        }
        try {
            DB::beginTransaction();
            $country->delete();
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
