<?php

namespace App\Providers;

use App\Models\ChecklistItem;
use App\Models\InspectionChecklist;
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
                ->applyRoleFilter()
                ->admin()
                ->firstOrFail();
        });

        Route::bind('staff', function ($uuid) {
            return User::whereUuid($uuid)
                ->whereNot('uuid', auth()->user()->uuid)
                ->applyRoleFilter()
                ->staff()
                ->firstOrFail();
        });

        Route::bind('vehicle', function ($uuid) {
            return Vehicle::whereUuid($uuid)
                ->applyRoleFilter()
                ->with(['inspections', 'user'])
                ->firstOrFail();
        });

        Route::bind('checklist', function ($uuid) {
            return InspectionChecklist::whereUuid($uuid)
                ->firstOrFail();
        });

        Route::bind('checklistItem', function ($uuid) {
            return ChecklistItem::whereUuid($uuid)
                ->firstOrFail();
        });
    }
}
