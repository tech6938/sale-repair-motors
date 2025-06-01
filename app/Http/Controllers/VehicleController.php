<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\View\View;
use App\Models\Inspection;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Models\InspectionChecklist;
use App\Services\DataTableActionLinksService;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('vehicles.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        $checklists = InspectionChecklist::get();

        return view('vehicles.show', compact('vehicle', 'checklists'));
    }

    /**
     * Display the checklist for the specified vehicle.
     */
    public function checklist(Vehicle $vehicle, InspectionChecklist $checklist)
    {
        $items = ChecklistItem::whereInspectionChecklistId($checklist->id)
            ->whereHas(
                'checklistItemResults.inspectionChecklistResult.inspection.vehicle',
                fn($q) => $q->where('id', $vehicle->id)
            )
            ->with('checklistItemResults', fn($q) => $q->whereHas(
                'inspectionChecklistResult.inspection.vehicle',
                fn($q) => $q->where('id', $vehicle->id)
            ))
            ->with('itemOptions')
            ->ordered()
            ->get();

        return view('vehicles.checklist', compact('vehicle', 'checklist', 'items'));
    }

    /**
     * Return the listing of the resource.
     */
    public function dataTable(Request $request): JsonResponse
    {
        $vehicles = Vehicle::applyRoleFilter()
            ->whereHas('inspections', function ($query) {
                $query->where('status', Inspection::STATUS_COMPLETED);
            })
            ->with('user', 'inspections');

        $dt = DataTables::of($vehicles);

        $dt->filter(function ($query) use ($request) {
            if (empty($request->input('search'))) return;

            $search = trim($request->input('search')['value']);
            $keywords = explode(' ', $search);

            $query->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->orWhere('make', 'like', "%$word%")
                        ->orWhere('model', 'like', "%$word%")
                        ->orWhere('color', 'like', "%$word%")
                        ->orWhere('year', 'like', "%$word%")
                        ->orWhere('address', 'like', "%$word%");
                }
            });
        });

        if (auth()->user()->isSuperAdmin()) {
            $dt->addColumn('manager', function ($record) {
                if ($record->user->id == auth()->user()->id) {
                    return '<div class="user-card">
                                <div class="user-avatar ' . getRandomColorClass() . '">
                                    ' . getAvatarHtml($record->user) . '
                                </div>
                                <div class="user-info">
                                    <span class="tb-lead">' . $record->user->name . '</span>
                                    <span>' . $record->user->email . '</span>
                                </div>
                            </div>';
                }

                return '<div class="user-card">
                        <div class="user-avatar ' . getRandomColorClass() . ' d-none d-sm-flex">
                            ' . getAvatarHtml($record->user) . '
                        </div>
                        <div class="user-info">
                            <span class="tb-lead">
                                <a href="' . route('staffs.show', $record->user->uuid) . '" async-modal async-modal-size="lg">
                                    ' . $record->user->name . '
                                </a>
                            </span>
                            <span>' . $record->user->email . '</span>
                        </div>
                    </div>';
            });
        }

        $dt->addColumn('vehicle', function ($record) {
            return '<span class="tb-product">
                        <a href="' . $record->image_url . '" class="popup-image">
                            <img src="' . getImageUrlByPath($record->image, true) . '" class="thumb" onerror="_ie(this)">
                        </a>
                        <div class="user-info">
                            <span class="tb-lead">
                                <a href="' . route('vehicles.show', $record->uuid) . '">
                                    ' . implode(' ', [$record->make, $record->model]) . '
                                </a>
                            </span>
                            <span>' . implode(' | ', [$record->year, $record->color]) . '</span>
                        </div>
                    </span>';
        });

        $dt->addColumn('address', function ($record) {
            return $record->address;
        });

        $dt->addColumn('started_at', function ($record) {
            return frontendDateTime($record->inspections->first()?->started_at);
        });

        $dt->addColumn('completed_at', function ($record) {
            return frontendDateTime($record->inspections->first()?->completed_at);
        });

        $dt->addColumn('created', function ($record) {
            return $record->createdAt();
        });

        $dt->addColumn('updated', function ($record) {
            return $record->updatedAt();
        });

        $dt->addColumn('actions', function ($record) {
            $links = [
                ['action' => 'view', 'syncResponse' => true],
            ];

            return (new DataTableActionLinksService(
                model: $record,
                routeNamespace: 'vehicles',
                datatableId: '#vehicles-dt',
                isLocked: false
            ))->byArray($links);
        });

        $dt->addIndexColumn();

        $dt->rawColumns(['actions', 'vehicle', 'address', 'started_at', 'completed_at', 'manager', 'created', 'updated']);

        return $dt->make(true);
    }
}
