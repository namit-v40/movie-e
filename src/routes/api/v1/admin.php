<?php

use App\Http\Controllers\API\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::resource('/user', UserController::class)->only('index', 'show');
});
