<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $movies = Movie::query()
            ->when($request->search, fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->with(['Country', 'Type', 'Director'])
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $movies,
        ]);
    }

    public function show($id): JsonResponse
    {
        $movie = Movie::with(['Country', 'Type', 'Director'])->find($id);

        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Movie not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $movie,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:movies,slug',
            'original_name' => 'required|string',
            'description' => 'nullable|string',
            'poster' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'release_year' => 'required|integer',
            'duration' => 'nullable|integer',
            'episode_current' => 'required|string',
            'episode_total' => 'required|string',
            'quality' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'type_id' => 'required|exists:types,id',
            'director_id' => 'required|exists:directors,id',
        ]);

        try {
            $movie = Movie::create($validated);

            return response()->json([
                'success' => true,
                'data' => $movie,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create movie',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Movie not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'slug' => 'sometimes|required|string|unique:movies,slug,' . $movie->id,
            'original_name' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'poster' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'release_year' => 'sometimes|required|integer',
            'duration' => 'nullable|integer',
            'episode_current' => 'sometimes|required|string',
            'episode_total' => 'sometimes|required|string',
            'quality' => 'sometimes|required|string',
            'country_id' => 'sometimes|required|exists:countries,id',
            'type_id' => 'sometimes|required|exists:types,id',
            'director_id' => 'sometimes|required|exists:directors,id',
        ]);

        try {
            $movie->update($validated);

            return response()->json([
                'success' => true,
                'data' => $movie,
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update movie',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json([
                'success' => false,
                'message' => 'Movie not found',
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $movie->delete();

            return response()->json([
                'success' => true,
                'message' => 'Movie deleted successfully',
            ]);
        } catch (\Throwable $th) {
            Log::error($th);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete movie',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
