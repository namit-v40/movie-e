<?php

use App\Http\Controllers\API\Admin\UserController;
use App\Http\Controllers\API\Admin\AccountController;
use App\Http\Controllers\API\Admin\CountryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::resource('/user', UserController::class)->only('index', 'show');
    Route::get('/account', [AccountController::class, 'account']);

    Route::resource('country', CountryController::class);
});
