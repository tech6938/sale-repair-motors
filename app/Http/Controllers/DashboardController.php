<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'adminsCount' => User::applyRoleFilter()->admin()->count(),
            'latestAdmins' => User::applyRoleFilter()->admin()->latest()->limit(5)->get(),

            'staffsCount' => User::applyRoleFilter()->staff()->count(),
            'latestStaffs' => User::applyRoleFilter()->staff()->latest()->limit(5)->get(),
        ]);
    }
}
