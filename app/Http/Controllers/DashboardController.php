<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\User;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'adminsCount' => User::applyRoleFilter()->admin()->count(),
            'staffsCount' => User::applyRoleFilter()->staff()->count(),
            'inspectionsCount' => Vehicle::applyRoleFilter()->whereHas(
                'inspections',
                fn($q) => $q->where('status', Inspection::STATUS_COMPLETED)
            )->count(),
        ]);
    }
}
