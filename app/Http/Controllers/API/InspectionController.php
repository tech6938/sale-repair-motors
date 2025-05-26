<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Checklist\ChecklistCollection;
use App\Http\Resources\ChecklistItem\ChecklistItemCollection;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\ChecklistItem;
use App\Models\InspectionChecklist;
use App\Models\InspectionType;
use Illuminate\Routing\Controllers\HasMiddleware;

class InspectionController extends BaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::ROLE_STAFF),
        ];
    }

    public function checklist(Vehicle $vehicle)
    {
        $inspectionType = InspectionType::whereHas('inspectionChecklists')->firstOrFail();

        $checklist = InspectionChecklist::whereInspectionTypeId($inspectionType->id)
            ->with(
                'inspectionChecklistResults',
                fn($q) => $q->whereHas(
                    'inspection.vehicle',
                    fn($q) => $q->where('id', $vehicle->id)
                )
            )
            ->ordered()
            ->get();

        return $this->apiResponse(
            'Checklists fetched successfully.',
            JsonResponse::HTTP_OK,
            new ChecklistCollection($checklist)
        );
    }

    public function items(Vehicle $vehicle, InspectionChecklist $checklist)
    {
        $items = ChecklistItem::whereInspectionChecklistId($checklist->id)
            ->with(
                'inspectionChecklist.inspectionChecklistResults',
                fn($q) => $q->whereHas(
                    'inspection.vehicle',
                    fn($q) => $q->where('id', $vehicle->id)
                )
            )
            ->ordered()
            ->get();

        return $this->apiResponse(
            'Checklist items fetched successfully.',
            JsonResponse::HTTP_OK,
            new ChecklistItemCollection($items)
        );
    }
}
