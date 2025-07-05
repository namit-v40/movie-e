<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\User\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Route for guard user and admin
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Route for only guard user
Route::post('/register', [RegisterController::class, 'register']);
// Route::post('/forgot-password', [ForgotController::class, 'sendResetPasswordLink']);
// Route::post('/validate-reset-password', [ForgotController::class, 'validateResetToken']);
// Route::post('/reset-password', [ForgotController::class, 'resetPassword']);
Route::post('/email-verification', [RegisterController::class, 'verifyEmail']);
Route::post('/resend-email-verification', [RegisterController::class, 'resend'])->middleware('throttle:6,1');
