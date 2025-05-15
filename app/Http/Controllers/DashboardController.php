<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::ROLE_SUPER_ADMIN),
        ];
    }

    public function index()
    {
        return view('dashboard', [
            'adminsCount' => User::admin()->count(),
            'latestAdmins' => User::admin()->latest()->limit(5)->get(),

            'staffsCount' => User::staff()->count(),
            'latestStaffs' => User::staff()->latest()->limit(5)->get(),
        ]);
    }
}
