<?php

namespace App\Providers;

use App\Models\Term;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class LayoutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('layouts.app', function ($view) {
            $view->with([
                'isDarkMode' => !empty(auth()->user()->settings()->where([
                    'key' => 'is_dark_mode',
                    'value' => true,
                ])->first()),
                'isCompactSidebar' => !empty(auth()->user()->settings()->where([
                    'key' => 'is_compact_sidebar',
                    'value' => true,
                ])->first()),
            ]);
        });
    }
}
