<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\View\View;
use App\Models\Inspection;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\ChecklistItemResult;
use App\Models\InspectionChecklist;
use App\Services\DataTableActionLinksService;
use App\Traits\FileUploader;

class VehicleController extends Controller
{
    use FileUploader;

    /**
     * Display the vehicles management index view.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('vehicles.index');
    }

    /**
     * Display the specified vehicle's details and associated checklists.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\View\View
     */
    public function show(Vehicle $vehicle)
    {
        $checklists = InspectionChecklist::get();

        return view('vehicles.show', compact('vehicle', 'checklists'));
    }

    /**
     * Display the specified vehicle's checklist items for the given checklist.
     *
     * @param \App\Models\Vehicle $vehicle
     * @param \App\Models\InspectionChecklist $checklist
     * @return \Illuminate\View\View
     */
    public function checklist(Vehicle $vehicle, InspectionChecklist $checklist)
    {
        $items = ChecklistItem::whereInspectionChecklistId($checklist->id)
            ->where(function ($query) use ($vehicle) {
                $query->whereIn('item_type', [ChecklistItem::ITEM_TYPE_SELECT, ChecklistItem::ITEM_TYPE_MULTISELECT])
                    ->orWhereHas(
                        'checklistItemResults.inspectionChecklistResult.inspection.vehicle',
                        fn($q) => $q->where('id', $vehicle->id)
                    );
            })
            ->with('itemOptions')
            ->with(['checklistItemResults' => function ($query) use ($vehicle) {
                $query->whereHas(
                    'inspectionChecklistResult.inspection.vehicle',
                    fn($q) => $q->where('id', $vehicle->id)
                );
            }])
            ->ordered()
            ->get();

        return view('vehicles.checklist', compact('vehicle', 'checklist', 'items'));
    }

    /**
     * Generates a PDF report for the given vehicle's latest completed inspection.
     * 
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function export(Vehicle $vehicle)
    {
        $inspection = $vehicle->inspections()
            ->where('status', Inspection::STATUS_COMPLETED)
            ->latest()
            ->first();

        if (empty($inspection)) {
            return back()->withErrors(['error' => 'No completed inspection found for this vehicle.']);
        }

        $checklists = InspectionChecklist::whereInspectionTypeId($inspection->inspectionType->id)
            ->with([
                'checklistItems' => function ($q) use ($vehicle) {
                    $q->where(function ($query) use ($vehicle) {
                        $query->whereIn('item_type', [ChecklistItem::ITEM_TYPE_SELECT, ChecklistItem::ITEM_TYPE_MULTISELECT])
                            ->orWhereHas(
                                'checklistItemResults.inspectionChecklistResult.inspection.vehicle',
                                fn($q) => $q->where('id', $vehicle->id)
                            );
                    })
                        ->with('itemOptions')
                        ->with(['checklistItemResults' => function ($query) use ($vehicle) {
                            $query->whereHas(
                                'inspectionChecklistResult.inspection.vehicle',
                                fn($q) => $q->where('id', $vehicle->id)
                            );
                        }])
                        ->ordered();
                }
            ])
            ->ordered()
            ->get();

        if ($checklists->isEmpty()) {
            return back()->withErrors(['error' => 'No checklist templates found for this inspection type.']);
        }

        // Verify we have results for each checklist
        foreach ($checklists as $checklist) {
            if ($checklist->inspectionChecklistResults->isEmpty()) {
                return back()->withErrors(['error' => "Missing results for checklist: {$checklist->name}"]);
            }

            // Verify we have results for all required items
            $missingRequiredItems = $checklist->checklistItems()
                ->where('is_required', true)
                ->whereDoesntHave('checklistItemResults', function ($q) use ($inspection) {
                    $q->whereHas('inspectionChecklistResult', fn($q) => $q->where('inspection_id', $inspection->id));
                })
                ->exists();

            if ($missingRequiredItems) {
                return back()->withErrors(['error' => "Missing required items in checklist: {$checklist->name}"]);
            }
        }

        $pdf = PDF::loadView('pdfs.inspection', [
            'vehicle' => $vehicle,
            'inspection' => $inspection,
            'checklists' => $checklists,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        // Add page numbers
        $canvas = $pdf->getCanvas();
        $fontMetrics = $pdf->getFontMetrics();
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $size = 10;

        $canvas->page_text(
            $canvas->get_width() - 70,
            $canvas->get_height() - 30,
            '{PAGE_NUM}/{PAGE_COUNT}',
            $font,
            $size
        );

        return $pdf->stream(sprintf(
            'inspection-report-%s-%s.pdf',
            $vehicle->uuid,
            $inspection->completed_at->format('Y-m-d')
        ));
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * Removes the vehicle, and any associated media items.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Vehicle $vehicle)
    {
        DB::beginTransaction();

        $items = ChecklistItemResult::whereHas(
            'inspectionChecklistResult.inspection.vehicle',
            fn($q) => $q->where('id', $vehicle->id)
        )->whereHas(
            'checklistItem',
            fn($q) => $q->whereIn('item_type', [
                ChecklistItem::ITEM_TYPE_IMAGE,
                ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                ChecklistItem::ITEM_TYPE_VIDEO,
            ])
        )->get();

        $vehicle->delete();

        foreach ($items as $item) {
            if (empty($item->value)) continue;

            if (is_array($item->value)) {
                foreach ($item->value as $filePath) {
                    $this->removePublicImage($filePath);
                }
            } else {
                $this->removePublicImage($item->value);
            }
        }

        DB::commit();

        return $this->jsonResponse(['message' => 'Vehicle deleted successfully.']);
    }

    /**
     * Returns the vehicle datatable.
     *
     * The edit link is only visible if the logged in user is not the vehicle owner.
     * The delete link is only visible if the logged in user is not the vehicle owner.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataTable(Request $request): JsonResponse
    {
        $vehicles = Vehicle::applyRoleFilter()
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
                        ->orWhere('year', 'like', "%$word%");
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

        $dt->addColumn('registration', function ($record) {
            return $record->registration;
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
                ['action' => 'delete', 'shouldRender' => auth()->user()->isSuperAdmin()],
            ];

            return (new DataTableActionLinksService(
                model: $record,
                routeNamespace: 'vehicles',
                datatableId: '#vehicles-dt',
                isLocked: false
            ))->byArray($links);
        });

        $dt->addIndexColumn();

        $dt->rawColumns(['actions', 'manager', 'vehicle', 'registration', 'started_at', 'completed_at', 'created', 'updated']);

        return $dt->make(true);
    }
}
