<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use App\Http\Resources\VehicleAssignResource;
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
            new Middleware('role:' . User::ROLE_PREPARATION_MANAGER),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function assign(Request $request)
{
    $validator = Validator::make($request->all(), [
        'vehicle_id' => 'required|exists:vehicles,id',
        'preparation_staff_id' => 'required|exists:users,id|different:preparation_manager_id',
    ]);

    if ($validator->fails()) {
        return $this->apiResponse(
            'Validation failed.',
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            $validator->errors()
        );
    }

    $vehicleId = $request->vehicle_id;
    $staffId = $request->preparation_staff_id;
    $managerId = auth()->id();

    $assignmentsCount = VehicleAssign::where('vehicle_id', $vehicleId)->count();
    if ($assignmentsCount >= 2) {
        return $this->apiResponse(
            'This vehicle already has the maximum of 2 staff assigned.',
            JsonResponse::HTTP_CONFLICT
        );
    }

    $alreadyAssigned = VehicleAssign::where('vehicle_id', $vehicleId)
        ->where('preparation_staff_id', $staffId)
        ->exists();

    if ($alreadyAssigned) {
        return $this->apiResponse(
            'This staff member is already assigned to this vehicle.',
            JsonResponse::HTTP_CONFLICT
        );
    }

    // Step 4: Save assignment
    $assignment = VehicleAssign::create([
        'vehicle_id' => $vehicleId,
        'preparation_manager_id' => $managerId,
        'preparation_staff_id' => $staffId,
    ]);

    // Step 5: Return success
    return $this->apiResponse(
        'Vehicle assigned successfully.',
        JsonResponse::HTTP_CREATED,
        new VehicleAssignResource($assignment)
    );
}

}
