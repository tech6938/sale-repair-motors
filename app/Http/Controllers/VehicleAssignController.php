<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\BaseController;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\Vehicle\VehicleResource;
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
            'vehicle_id' => 'required|exists:vehicles,uuid',
            'user_id' => 'required|array|min:1|max:2',
            'user_id.*' => 'required|uuid|exists:users,uuid',
        ]);

        DB::beginTransaction();

        foreach ($validate['user_ids'] as $user_id) {
            $vehicle = VehicleAssign::create([
                'vehicle_id' => $validate['vehicle_id'],
                'user_id'    => $user_id,
            ]);
        }

        return $this->apiResponse(
            'Vehicle Assigned successfully.',
            JsonResponse::HTTP_CREATED,
            new VehicleResource($vehicle)
        );
    }
}
