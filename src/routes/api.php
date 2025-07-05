<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1/auth.php';
    require __DIR__ . '/api/v1/public.php';
    Route::middleware('auth')->group(function () {
        require __DIR__ . '/api/v1/channels.php';
    });
    // Route::middleware('auth:user')->group(function () {
    //     Route::middleware(['auth:user', 'role:user,creator'])->group(function () {
    //         require __DIR__ . '/api/v1/user.php';
    //     });
    //     Route::middleware(['auth:user', 'role:creator'])->group(function () {
    //         require __DIR__ . '/api/v1/creator.php';
    //     });
    // });
    Route::middleware('auth:admin')->group(function () {
        require __DIR__ . '/api/v1/admin.php';
    });
});

Route::prefix('v2')->group(function () {
    // code...
});
