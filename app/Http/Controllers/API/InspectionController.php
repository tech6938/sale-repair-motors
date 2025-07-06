<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Inspection;
use App\Traits\FileUploader;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use App\Models\InspectionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\ChecklistItemResult;
use App\Models\InspectionChecklist;
use App\Models\InspectionChecklistResult;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\Checklist\ChecklistCollection;
use App\Http\Resources\ChecklistItem\ChecklistItemCollection;

class InspectionController extends BaseController implements HasMiddleware
{
    use FileUploader;

    /**
     * Get the middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . implode('|',  [User::ROLE_ADMIN, User::ROLE_STAFF]), only: ['checklists', 'items']),
            new Middleware('role:' . User::ROLE_STAFF, only: ['store']),
        ];
    }

    /**
     * Fetch all checklists for a given vehicle.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Fetch all items for a given checklist of a vehicle.
     *
     * @param \App\Models\Vehicle $vehicle
     * @param \App\Models\InspectionChecklist $checklist
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
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

    /**
     * Store a new inspection item.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vehicle $vehicle
     * @param \App\Models\ChecklistItem $item
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function store(Request $request, Vehicle $vehicle, ChecklistItem $item)
    {
        if ($vehicle->hasCompletedInspection()) {
            throw new \Exception('This vehicle has already completed an inspection.');
        }

        if (! $item->inspectionChecklist->isPreviousChecklistCompleted($vehicle)) {
            throw new \Exception('Please complete the previous checklist before proceeding.');
        }

        $request->validate($this->getValidationRules($item));

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
        $value = $this->processValue($request, $vehicle, $item);

        $checklistResult->checklistItemResults()
            ->updateOrCreate([
                'checklist_item_id' => $item->id,
            ], [
                'value' => $value,
            ]);

        if (
            $item->item_type === ChecklistItem::ITEM_TYPE_SELECT &&
            (
                str_contains(strtolower($item->title), 'mechanical fault') ||
                str_contains(strtolower($item->description), 'mechanical fault')
            )
        ) {
            $option = $item->itemOptions->first(function ($option) use ($value) {
                return $option->uuid === $value;
            });

            $vehicle->update([
                'mechanical_fault' => $option && strtolower($option->label) === 'yes'
            ]);
        }

        if (
            $item->item_type === ChecklistItem::ITEM_TYPE_SELECT &&
            (
                str_contains(strtolower($item->title), 'bodywork damage') ||
                str_contains(strtolower($item->description), 'bodywork damage')
            )
        ) {
            $option = $item->itemOptions->first(function ($option) use ($value) {
                return $option->uuid === $value;
            });

            $vehicle->update([
                'bodywork_damage' => $option && strtolower($option->label) === 'yes'
            ]);
        }

        // Update checklist and inspection status if needed
        $this->updateCompletionStatus($checklistResult, $inspection);

        // Refresh the inspection to get the latest status
        $inspection->refresh();

        if ($inspection->status === Inspection::STATUS_COMPLETED) {
            auth()->user()->manager->sendFirebaseNotification(
                'Inspection Completed!',
                'A new inspection has been submitted in your account.',
            );
        }

        DB::commit();

        return $this->apiResponse(
            'Inspection item saved successfully.',
            JsonResponse::HTTP_OK,
        );
    }

    /**
     * Returns the validation rules for a given checklist item.
     *
     * @param \App\Models\ChecklistItem  $item
     * @return array
     */
    private function getValidationRules(ChecklistItem $item): array
    {
        $requiredNullable = $item->is_required ? 'required' : 'nullable';

        if ($item->item_type === ChecklistItem::ITEM_TYPE_IMAGE) {
            return [
                'value' => [$requiredNullable, 'file', 'mimes:' . config('constants.image_mimes'), 'max:' . config('constants.max_image_size')],
            ];
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_MULTI_IMAGE) {
            $min = $item->min ?? 1;
            $max = $item->max ?? 5;

            return [
                'value' => [$requiredNullable, 'array', "min:$min", "max:$max"],
                'value.*' => ['file', 'mimes:' . config('constants.image_mimes'), 'max:' . config('constants.max_image_size')],
            ];
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_VIDEO) {
            return [
                'value' => [$requiredNullable, 'file', 'mimetypes:' . config('constants.video_mimetypes'), 'max:' . config('constants.max_video_size')]
            ];
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_TEXT) {
            return [
                'value' => [$requiredNullable, 'string', 'max:1000']
            ];
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_NUMBER) {
            return [
                'value' => [$requiredNullable, 'numeric']
            ];
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_BOOLEAN) {
            // If boolean is required, it can only be 'yes'
            if ($requiredNullable === 'required') {
                return [
                    'value' => [$requiredNullable, 'in:yes']
                ];
            }

            return [
                'value' => [$requiredNullable, 'in:yes,no']
            ];
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_SELECT) {
            return [
                'value' => [$requiredNullable, 'in:' . implode(',', $item->itemOptions?->pluck('uuid')->toArray())]
            ];
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_MULTISELECT) {
            return [
                'value' => [$requiredNullable, 'array', 'in:' . implode(',', $item->itemOptions?->pluck('uuid')->toArray())]
            ];
        }

        return ['value' => 'nullable'];
    }

    /**
     * Processes the given value for the given checklist item and vehicle.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vehicle $vehicle
     * @param \App\Models\ChecklistItem $item
     * @return mixed
     */
    private function processValue(Request $request, Vehicle $vehicle, ChecklistItem $item)
    {
        $previousValue = ChecklistItemResult::whereHas('checklistItem', fn($q) => $q->where('id', $item->id))
            ->whereHas(
                'inspectionChecklistResult.inspection.vehicle',
                fn($q) => $q->where('id', $vehicle->id)
            )
            ->first()?->value;

        if ($item->item_type === ChecklistItem::ITEM_TYPE_IMAGE) {
            if (empty($request->file('value'))) {
                return null;
            }

            return $this->uploadPublicImage($request->file('value'), 'inspections', $previousValue);
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_MULTI_IMAGE) {
            if (empty($request->file('value')) || !is_array($request->file('value'))) {
                return null;
            }

            $paths = [];
            foreach ($request->file('value') as $file) {
                $paths[] = $this->uploadPublicImage($file, 'inspections');
            }

            if (!empty($previousValue) && is_array($previousValue)) {
                foreach ($previousValue as $path) {
                    $this->removePublicImage($path);
                }
            }

            return $paths;
        }

        if ($item->item_type === ChecklistItem::ITEM_TYPE_VIDEO) {
            if (empty($request->file('value'))) {
                return null;
            }

            return $this->uploadPublicFile($request->file('value'), 'inspections', $previousValue);
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

    /**
     * Updates the completion status of a checklist result and its associated inspection.
     *
     * If all required checklist items are completed, marks the checklist result as completed.
     * Additionally, if all checklists in the inspection are completed, marks the inspection 
     * as completed.
     *
     * @param \App\Models\InspectionChecklistResult $checklistResult
     * @param \App\Models\Inspection $inspection
     */
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
