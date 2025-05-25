<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        Route::bind('admin', function ($uuid) {
            return User::whereUuid($uuid)
                ->whereNot('uuid', auth()->user()->uuid)
                ->managedByUser()
                ->admin()
                ->firstOrFail();
        });

        Route::bind('staff', function ($uuid) {
            return User::whereUuid($uuid)
                ->whereNot('uuid', auth()->user()->uuid)
                ->managedByUser()
                ->staff()
                ->firstOrFail();
        });

        Route::bind('vehicle', function ($uuid) {
            return Vehicle::whereUuid($uuid)
                ->managedByUser()
                ->with(['inspections', 'user'])
                ->firstOrFail();
        });
    }
}
