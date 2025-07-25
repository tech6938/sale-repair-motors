<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\InspectionController;
use App\Http\Controllers\API\Profile\ProfileController;
use App\Http\Controllers\API\Profile\PasswordController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\ManagerStaffController;
use App\Http\Controllers\VehicleAssignController;
use App\Http\Controllers\API\PreparationStaffController;

Route::middleware(['guest', 'throttle:50,1'])->group(function () {
    // Login
    Route::controller(LoginController::class)->group(function () {
        Route::post('/login', 'login');
    });

    // Forgot password
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::post('/forgot-password', 'sendOtp');
        Route::post('/forgot-password/update', 'updatePassword');
    });
});

Route::middleware(['auth:sanctum', 'suspended'])->group(function () {
    // Profile
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'view');
        Route::post('/', 'update');
    });

    // Change password
    Route::controller(PasswordController::class)->group(function () {
        Route::post('/profile/password/change', 'change');
    });

    // Logout
    Route::controller(LoginController::class)->group(function () {
        Route::post('/logout', 'logout');
    });

    // Admins
    Route::prefix('admins')->controller(AdminController::class)->group(function () {
        Route::get('/', 'list');
        Route::post('/', 'store');
        Route::get('/{admin}', 'show');
        Route::post('/{admin}', 'update');
        Route::delete('/{admin}', 'destroy');
    });

    // Manager Staffs
    Route::prefix('admin/manager-staffs')->controller(ManagerStaffController::class)->group(function () {
        Route::get('/', 'list');
        Route::post('/', 'store');
    });

    // Staffs
    Route::prefix('admin/staffs')->controller(StaffController::class)->group(function () {
        Route::get('/', 'list');
        Route::post('/', 'store');
        Route::get('/{staff}', 'show');
        Route::post('/{staff}', 'update');
        Route::delete('/{staff}', 'destroy');
    });

    // Vehicles
    Route::prefix('vehicles')->group(function () {
        Route::controller(VehicleController::class)->group(function () {
            Route::get('/', 'list');
            Route::post('/', 'store');
            Route::get('/{vehicle}', 'show');
            Route::post('/{vehicle}', 'update');
            Route::delete('/{vehicle}', 'destroy');
        });

        // Vehicle inspections
        Route::prefix('/{vehicle}')->controller(InspectionController::class)->group(function () {
            Route::get('/checklists', 'checklists');
            Route::get('/checklists/{checklist}', 'items');
            Route::post('/checklists-items/{checklistItem}', 'store');
        });

        // Vehicle assign
        Route::prefix('/assign')->controller(VehicleAssignController::class)->group(function () {
            Route::post('/store', 'store');
        });
    });

    Route::prefix('preparation_staff')->name('preparation_staff.')->group(function () {
        Route::post('/', [PreparationStaffController::class, 'create_staff']);
    });
});
