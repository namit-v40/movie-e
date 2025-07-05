<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function example(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Example',
        ]);
    }
}
