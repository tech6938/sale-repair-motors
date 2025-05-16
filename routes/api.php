<?php

use App\Http\Controllers\API as Controllers;
use Illuminate\Support\Facades\Route;

Route::middleware('guest', 'throttle:5,1')->group(function () {
    // Login route
    Route::post('/login', [Controllers\Auth\LoginController::class, 'login']);

    // Forgot password routes
    Route::post('/forgot-password', [Controllers\Auth\ForgotPasswordController::class, 'sendOtp']);
    Route::post('/forgot-password/update', [Controllers\Auth\ForgotPasswordController::class, 'updatePassword']);
});

Route::middleware('auth:sanctum', 'verified', 'suspended')->group(function () {
    // Profile routes
    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [Controllers\Profile\ProfileController::class, 'view']);
        Route::post('/', [Controllers\Profile\ProfileController::class, 'update']);

        // Update password routes
        Route::post('/password/change', [Controllers\Profile\PasswordController::class, 'change']);
    });

    // Logout route
    Route::post('/logout', [Controllers\Auth\LoginController::class, 'logout']);
});
