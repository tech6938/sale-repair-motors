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

   public function assignToStaff(Request $request)
{
    $validator = Validator::make($request->all(), [
        'vehicle_id' => 'required|exists:vehicles,id',
        'preparation_staff_id' => 'required|array|min:1|max:2',
        'preparation_staff_id.*' => 'exists:users,id|different:preparation_manager_id',
    ]);

    if ($validator->fails()) {
        return $this->apiResponse(
            'Validation failed.',
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            $validator->errors()
        );
    }

    $vehicleId = $request->vehicle_id;
    $staffIds = $request->preparation_staff_id;
    $managerId = auth()->id();

    // ✅ 1. Check manager's assigned staff
    $allowedStaffIds = \App\Models\ManagerAssign::where('manager_id', $managerId)
        ->pluck('staff_id')
        ->toArray();

    $invalidStaffIds = array_diff($staffIds, $allowedStaffIds);

    if (count($invalidStaffIds)) {
        return $this->apiResponse(
            'You can only assign staff under your management. Invalid staff IDs: ' . implode(',', $invalidStaffIds),
            JsonResponse::HTTP_FORBIDDEN
        );
    }

    // ✅ 2. Manager cannot assign themselves
    if (in_array($managerId, $staffIds)) {
        return $this->apiResponse(
            'Manager cannot be assigned as preparation staff.',
            JsonResponse::HTTP_CONFLICT
        );
    }

    // ✅ 3. Fetch all current assignments in 1 query
    $existingAssignments = VehicleAssign::where('vehicle_id', $vehicleId)->get();

    $assignedStaffIds = $existingAssignments->pluck('preparation_staff_id')->filter()->toArray();
    $existingSlots = $existingAssignments->where('preparation_manager_id', $managerId)->whereNull('preparation_staff_id');

    // ✅ 4. Check if adding exceeds 2 staff
    if ((count($assignedStaffIds) + count($staffIds)) > 2) {
        return $this->apiResponse(
            'This vehicle already has assigned staff or exceeds the limit of 2.',
            JsonResponse::HTTP_CONFLICT
        );
    }

    // ✅ 5. Check if any staff is already assigned
    $alreadyAssigned = array_intersect($staffIds, $assignedStaffIds);
    if (!empty($alreadyAssigned)) {
        return $this->apiResponse(
            'Staff member(s) already assigned to this vehicle: ' . implode(',', $alreadyAssigned),
            JsonResponse::HTTP_CONFLICT
        );
    }

    // ✅ 6. Assign staff
    $assigned = [];

    foreach ($staffIds as $staffId) {
        $slot = $existingSlots->shift(); // use empty slot first if available

        if ($slot) {
            $slot->update(['preparation_staff_id' => $staffId]);
            $assigned[] = $slot;
        } else {
            $assigned[] = VehicleAssign::create([
                'vehicle_id' => $vehicleId,
                'preparation_manager_id' => $managerId,
                'preparation_staff_id' => $staffId,
            ]);
        }
    }

    return $this->apiResponse(
        'Vehicle assigned successfully.',
        JsonResponse::HTTP_CREATED,
        VehicleAssignResource::collection($assigned)
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


    public function getVehiclesForStaff()
    {
        $staffId = auth()->id();

        $assignments = VehicleAssign::with(['vehicle', 'preparationStaff'])
            ->where('preparation_staff_id', $staffId)
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
