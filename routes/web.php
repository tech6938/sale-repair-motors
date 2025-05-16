<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers as Controllers;

Route::fallback(function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [Controllers\Auth\AuthenticatedSessionController::class, 'store'])->middleware('throttle:3,1');

    Route::get('forgot-password', [Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('throttle:5,1');

    Route::get('reset-password/{token}', [Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [Controllers\Auth\NewPasswordController::class, 'store'])->name('password.store')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware('auth', 'suspended')->group(function () {
    // Dashboard routes
    Route::get('dashboard', [Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Settings routes
    Route::post('settings', [Controllers\SettingController::class, 'store'])->name('settings.store');

    // Profile routes
    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/', [Controllers\Profile\ProfileController::class, 'index'])->name('index');
        Route::post('/', [Controllers\Profile\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [Controllers\Profile\ProfileController::class, 'update'])->name('update');

        // Avatar routes
        Route::post('/avatar', [Controllers\Profile\AvatarController::class, 'edit'])->name('avatar.edit');
        Route::put('/avatar', [Controllers\Profile\AvatarController::class, 'update'])->name('avatar.update');

        // Update password routes
        Route::post('/password', [Controllers\Profile\UpdatePasswordController::class, 'edit'])->name('password.edit');
        Route::put('/password', [Controllers\Profile\UpdatePasswordController::class, 'update'])->name('password.update');
    });

    // Admins routes
    Route::group(['prefix' => 'admins', 'as' => 'admins.'], function () {
        Route::post('resend-invitation/{admin}', [Controllers\AdminController::class, 'resendInvitation'])->name('resend-invitation');
        Route::post('comments/{admin}', [Controllers\AdminController::class, 'comments'])->name('comments');
        Route::get('datatable', [Controllers\AdminController::class, 'dataTable'])->name('datatable');
        Route::resource('/', Controllers\AdminController::class)->parameters(['' => 'admin']);
    });

    // Staffs routes
    Route::group(['prefix' => 'staffs', 'as' => 'staffs.'], function () {
        Route::post('resend-invitation/{staff}', [Controllers\StaffController::class, 'resendInvitation'])->name('resend-invitation');
        Route::post('comments/{staff}', [Controllers\StaffController::class, 'comments'])->name('comments');
        Route::get('datatable', [Controllers\StaffController::class, 'dataTable'])->name('datatable');
        Route::resource('/', Controllers\StaffController::class)->parameters(['' => 'staff']);
    });
});
