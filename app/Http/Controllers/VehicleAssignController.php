<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\BaseController;
use App\Models\VehicleAssign;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class VehicleAssignController extends BaseController implements HasMiddleware
{
    /**
     * Specify the middleware that is used by this controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::ROLE_ADMIN),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'vehicle_id' => 'required|string|max:50',
            'user_id' => 'required|email|max:255|exists:users,uuid',
        ]);

        DB::beginTransaction();

        VehicleAssign::create($validate);

        DB::commit();

        return $this->apiResponse(
            'Vehicle Assigned successfully.',
            JsonResponse::HTTP_OK,
            // new UserResource($admin)
        );
    }
}
