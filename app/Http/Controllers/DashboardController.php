<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'adminsCount' => User::managedByUser()->admin()->count(),
            'latestAdmins' => User::managedByUser()->admin()->latest()->limit(5)->get(),

            'staffsCount' => User::managedByUser()->staff()->count(),
            'latestStaffs' => User::managedByUser()->staff()->latest()->limit(5)->get(),
        ]);
    }
}
