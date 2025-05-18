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

Route::middleware('auth:sanctum', 'suspended')->group(function () {
    // Profile routes
    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [Controllers\Profile\ProfileController::class, 'view']);
        Route::post('/', [Controllers\Profile\ProfileController::class, 'update']);

        // Update password routes
        Route::post('/password/change', [Controllers\Profile\PasswordController::class, 'change']);
    });

    // Logout route
    Route::post('/logout', [Controllers\Auth\LoginController::class, 'logout']);

    // Admin routes
    Route::get('/admins', [Controllers\AdminController::class, 'list']);
    Route::post('/admins', [Controllers\AdminController::class, 'store']);
    Route::get('/admins/{admin}', [Controllers\AdminController::class, 'show']);
    Route::post('/admins/{admin}', [Controllers\AdminController::class, 'update']);
    Route::delete('/admins/{admin}', [Controllers\AdminController::class, 'destroy']);

    // Staff routes
    Route::get('/staffs', [Controllers\StaffController::class, 'list']);
    Route::post('/staffs', [Controllers\StaffController::class, 'store']);
    Route::get('/staffs/{staff}', [Controllers\StaffController::class, 'show']);
    Route::post('/staffs/{staff}', [Controllers\StaffController::class, 'update']);
    Route::delete('/staffs/{staff}', [Controllers\StaffController::class, 'destroy']);
});
