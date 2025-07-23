<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers as Controllers;

// Redirect unknown routes
Route::fallback(fn() => redirect()->route('dashboard'));

// Guest routes
Route::prefix('/')->middleware('guest')->group(function () {
    Route::get('login', [Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [Controllers\Auth\AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:3,1');

    Route::get('forgot-password', [Controllers\Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email')
        ->middleware('throttle:5,1');

    Route::get('reset-password/{token}', [Controllers\Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.store')
        ->middleware('throttle:5,1');
});

// Authenticated only
Route::middleware('auth')->group(function () {
    Route::post('logout', [Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Auth + not suspended
Route::middleware(['auth', 'suspended'])->group(function () {
    // Dashboard
    Route::get('dashboard', [Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Settings
    Route::post('settings', [Controllers\SettingController::class, 'store'])->name('settings.store');

    // Profile group
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [Controllers\Profile\ProfileController::class, 'index'])->name('index');
        Route::post('/', [Controllers\Profile\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [Controllers\Profile\ProfileController::class, 'update'])->name('update');

        Route::prefix('avatar')->name('avatar.')->group(function () {
            Route::post('/', [Controllers\Profile\AvatarController::class, 'edit'])->name('edit');
            Route::put('/', [Controllers\Profile\AvatarController::class, 'update'])->name('update');
        });

        Route::prefix('password')->name('password.')->group(function () {
            Route::post('/', [Controllers\Profile\UpdatePasswordController::class, 'edit'])->name('edit');
            Route::put('/', [Controllers\Profile\UpdatePasswordController::class, 'update'])->name('update');
        });
    });

    // Admins
    Route::prefix('admins')->middleware('role:super_admin')->name('admins.')->group(function () {
        Route::post('comments/{admin}', [Controllers\AdminController::class, 'comments'])->name('comments');
        Route::get('datatable', [Controllers\AdminController::class, 'dataTable'])->name('datatable');
        Route::resource('/', Controllers\AdminController::class)->parameters(['' => 'admin']);
    });

    // Manager's Staffs
    Route::prefix('managers-staffs')->name('managers-staffs.')->group(function () {
        Route::post('comments/{admin}', [Controllers\ManagerStaffController::class, 'comments'])->name('comments');
        Route::get('datatable', [Controllers\ManagerStaffController::class, 'dataTable'])->name('datatable');
        Route::resource('/', Controllers\ManagerStaffController::class)->parameters(['' => 'manager']);
    });

    // Staffs
    Route::prefix('staffs')->name('staffs.')->group(function () {
        Route::post('comments/{staff}', [Controllers\StaffController::class, 'comments'])->name('comments');
        Route::get('datatable', [Controllers\StaffController::class, 'dataTable'])->name('datatable');
        Route::resource('/', Controllers\StaffController::class)->parameters(['' => 'staff']);
    });

    // Vehicles
    Route::prefix('vehicles')->name('vehicles.')->group(function () {
        Route::get('datatable', [Controllers\VehicleController::class, 'dataTable'])->name('datatable');
        Route::get('/', [Controllers\VehicleController::class, 'index'])->name('index');
        Route::get('/{vehicle}', [Controllers\VehicleController::class, 'show'])->name('show');
        Route::get('/{vehicle}/export', [Controllers\VehicleController::class, 'export'])->name('export');
        Route::get('/{vehicle}/checklist/{checklist}', [Controllers\VehicleController::class, 'checklist'])->name('checklist');

        Route::delete('/{vehicle}', [Controllers\VehicleController::class, 'destroy'])
            ->name('destroy')
            ->middleware('role:super_admin');
    });
});
