<?php

namespace App\Http\Controllers\API;

use Faker\Provider\Base;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\VehicleAssignResource;

class VehicleAssignController extends BaseController
{
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

    // Step 3: Prevent duplicate assignment
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

public function getVehiclesForManager()
{
    $managerId = auth()->id();

    $assignments = VehicleAssign::with(['vehicle', 'preparationStaff'])
        ->where('preparation_manager_id', $managerId)
        ->get()
        ->groupBy('vehicle_id')
        ->map(function ($group) {
            return [
                'vehicle' => $group->first()->vehicle,
                'assigned_staff' => $group->pluck('preparationStaff'),
            ];
        })
        ->values();

    return $this->apiResponse(
        'Assigned vehicles fetched successfully.',
        JsonResponse::HTTP_OK,
        $assignments
    );
}


}
