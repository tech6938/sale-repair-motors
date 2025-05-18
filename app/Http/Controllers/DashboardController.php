<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'adminsCount' => User::ownedByUser()->admin()->count(),
            'latestAdmins' => User::ownedByUser()->admin()->latest()->limit(5)->get(),

            'staffsCount' => User::ownedByUser()->staff()->count(),
            'latestStaffs' => User::ownedByUser()->staff()->latest()->limit(5)->get(),
        ]);
    }
}
