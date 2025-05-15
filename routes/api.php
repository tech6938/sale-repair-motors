<?php

use App\Http\Controllers\API as Controllers;
use Illuminate\Support\Facades\Route;

Route::middleware('guest', 'throttle:5,1')->group(function () {
    Route::post('/register', [Controllers\Auth\RegisterController::class, 'register']);
    Route::post('/register/otp/resend', [Controllers\Auth\RegisterController::class, 'resendOtp']);
    Route::post('/register/otp/verify', [Controllers\Auth\RegisterController::class, 'verifyOtp']);

    Route::post('/login', [Controllers\Auth\LoginController::class, 'login']);

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

    Route::post('/logout', [Controllers\Auth\LoginController::class, 'logout']);
});
