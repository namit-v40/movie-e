<?php

use App\Http\Controllers\API\Admin\UserController;
use App\Http\Controllers\API\Admin\AccountController;
use App\Http\Controllers\API\Admin\CountryController;
use App\Http\Controllers\API\Admin\CategoryController;
use App\Http\Controllers\API\Admin\DirectorController;
use App\Http\Controllers\API\Admin\ActorController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::resource('/user', UserController::class)->only('index', 'show');
    Route::get('/account', [AccountController::class, 'account']);

    Route::resource('country', CountryController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('director', DirectorController::class);
    Route::resource('actor', ActorController::class);
});
