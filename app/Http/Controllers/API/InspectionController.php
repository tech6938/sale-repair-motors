<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Inspection;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use App\Models\InspectionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\InspectionChecklist;
use App\Models\InspectionChecklistResult;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\Checklist\ChecklistCollection;
use App\Http\Resources\ChecklistItem\ChecklistItemCollection;
use App\Traits\FileUploader;

class InspectionController extends BaseController implements HasMiddleware
{
    use FileUploader;

    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::ROLE_STAFF),
        ];
    }

    public function checklists(Vehicle $vehicle)
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
        if (! $checklist->isPreviousChecklistCompleted($vehicle)) {
            throw new \Exception('Please complete the previous checklist before proceeding.');
        }

        $items = ChecklistItem::whereInspectionChecklistId($checklist->id)
            ->with(
                'checklistItemResults',
                fn($q) => $q->whereHas(
                    'inspectionChecklistResult.inspection.vehicle',
                    fn($q) => $q->where('id', $vehicle->id)
                )
            )
            ->with('itemOptions')
            ->ordered()
            ->get();

        return $this->apiResponse(
            'Checklist items fetched successfully.',
            JsonResponse::HTTP_OK,
            new ChecklistItemCollection($items)
        );
    }

    public function store(Request $request, Vehicle $vehicle, ChecklistItem $item)
    {
        if ($vehicle->hasCompletedInspection()) {
            throw new \Exception('This vehicle has already completed an inspection.');
        }

        if (! $item->inspectionChecklist->isPreviousChecklistCompleted($vehicle)) {
            throw new \Exception('Please complete the previous checklist before proceeding.');
        }

        $request->validate([
            'value' => $this->getValidationRules($item),
        ]);

        $inspectionType = InspectionType::first();

        DB::beginTransaction();

        $inspection = $vehicle->inspections()
            ->firstOrCreate([
                'inspection_type_id' => $inspectionType->id,
            ], [
                'started_at' => now(),
                'status' => Inspection::STATUS_INCOMPLETE
            ]);

        $checklistResult = $inspection->inspectionChecklistResults()
            ->firstOrCreate([
                'inspection_checklist_id' => $item->inspectionChecklist->id,
            ], [
                'status' => InspectionChecklistResult::STATUS_INCOMPLETE
            ]);

        // Process the value based on type
        $value = $this->processValue($request, $item);

        $checklistResult->checklistItemResults()
            ->updateOrCreate([
                'checklist_item_id' => $item->id,
            ], [
                'value' => $value,
            ]);

        // Update checklist and inspection status if needed
        $this->updateCompletionStatus($checklistResult, $inspection);

        DB::commit();

        return $this->apiResponse(
            'Inspection item saved successfully.',
            JsonResponse::HTTP_OK,
        );
    }

    private function getValidationRules(ChecklistItem $item)
    {
        $rules = $item->is_required ? 'required' : 'nullable';

        switch ($item->item_type) {
            case 'image':
                $rules .= '|file|mimes:jpg,png,gif|max:' . config('constants.max_image_size');
                break;

            case 'video':
                $rules .= '|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-flv,video/x-matroska|max:' . config('constants.max_image_size');
                break;

            case 'text':
                $rules .= '|string|max:1000';
                break;

            case 'number':
                $rules .= '|numeric';
                break;

            case 'boolean':
                $rules .= '|in:yes,no';
                break;

            case 'select':
                $options = $item->itemOptions?->pluck('uuid')->toArray();
                $rules .= '|in:' . implode(',', $options);
                break;

            case 'multiselect':
                $options = $item->itemOptions?->pluck('uuid')->toArray();
                $rules .= '|array|in:' . implode(',', $options);
                break;
        }

        return $rules;
    }

    private function processValue(Request $request, ChecklistItem $item)
    {
        if ($item->item_type === ChecklistItem::ITEM_TYPE_IMAGE) {
            if (empty($request->file('value'))) {
                return null;
            }

            return $this->uploadPublicImage($request->file('value'), 'inspections', $item?->checklistItemResults()?->first()?->value);
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_VIDEO) {
            if (empty($request->file('value'))) {
                return null;
            }

            return $this->uploadPublicFile($request->file('value'), 'inspections', $item?->checklistItemResults()?->first()?->value);
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_TEXT) {
            return $request->input('value');
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_NUMBER) {
            return (float) $request->input('value');
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_BOOLEAN) {
            return $request->input('value') === 'yes' ? true : false;
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_SELECT) {
            return $request->input('value');
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_MULTISELECT) {
            if (empty($request->input('value'))) return null;

            $optionsUuids = $item->itemOptions()->pluck('uuid')->toArray();

            if (!empty(array_diff($request->input('value'), $optionsUuids))) {
                throw new \Exception('Some of the provided option values are incorrect.');
            }

            return $request->input('value');
        }

        return $request->input('value');
    }

    private function updateCompletionStatus(InspectionChecklistResult $checklistResult, Inspection $inspection)
    {
        // Update checklist result status if all required items are completed
        $completedItems = $checklistResult->checklistItemResults()->count();
        $totalRequiredItems = $checklistResult->inspectionChecklist->checklistItems()
            ->where('is_required', true)
            ->count();

        if ($completedItems >= $totalRequiredItems) {
            $checklistResult->update(['status' => InspectionChecklistResult::STATUS_COMPLETED]);
        }

        // Update inspection status if all checklists are completed
        $completedChecklists = $inspection->inspectionChecklistResults()
            ->where('status', InspectionChecklistResult::STATUS_COMPLETED)
            ->count();
        $totalChecklists = $inspection->inspectionType->inspectionChecklists()->count();

        if ($completedChecklists >= $totalChecklists) {
            $inspection->update([
                'status' => Inspection::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        }
    }
}
