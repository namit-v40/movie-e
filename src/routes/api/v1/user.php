<?php

use App\Http\Controllers\API\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {

    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'profile']);
    });

    Route::get('/{user_id}/profile', [UserController::class, 'getUserById']);
});

Route::resource('/user', UserController::class)->only('show');
