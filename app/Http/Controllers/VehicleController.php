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
            ->with(
                'checklistItems',
                function ($q) use ($vehicle) {
                    $q->whereHas(
                        'checklistItemResults.inspectionChecklistResult.inspection.vehicle',
                        fn($q) => $q->where('id', $vehicle->id)
                    )
                        ->with('checklistItemResults', fn($q) => $q->whereHas(
                            'inspectionChecklistResult.inspection.vehicle',
                            fn($q) => $q->where('id', $vehicle->id)
                        ))
                        ->with('itemOptions')
                        ->ordered();
                },
            )
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
                        ->orWhere('license_plate', 'like', "%$word%");
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

        $dt->addColumn('license_plate', function ($record) {
            return $record->license_plate;
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

        $dt->rawColumns(['actions', 'manager', 'vehicle', 'license_plate', 'started_at', 'completed_at', 'created', 'updated']);

        return $dt->make(true);
    }
}
