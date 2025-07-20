<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function account(): JsonResponse
    {
        return response()->json(Auth::user());
    }
}
