<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\BaseController;
use App\Services\DataTableActionLinksService;
use Illuminate\Routing\Controllers\Middleware;
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
            new Middleware('role:' . User::ROLE_SUPER_ADMIN),
        ];
    }

    // public function index(): View
    // {
    //     $assigns = VehicleAssign::with(['vehicle', 'preparationManager'])->latest()->get();
    //     return view('vehicle-to-manager.index', compact('assigns'));
    // }
    public function index(): View
    {
        // $assigns = VehicleAssign::with(['vehicle', 'preparationManager'])->latest()->get();
        return view('vehicle-to-manager.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'preparation_manager_id' => 'required|array|min:1|max:2',
                'preparation_manager_id.*' => 'required|exists:users,id',
            ]);


            $assigned = [];
            foreach ($validate['preparation_manager_id'] as $managerId) {
                $assigned[] = VehicleAssign::create([
                    'vehicle_id' => $validate['vehicle_id'],
                    'preparation_manager_id' => $managerId,
                ]);
            }

            DB::commit();

            return $this->jsonResponse(['message' => 'Vehicle has been Assigned successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    public function create()
    {
        $managers = User::preprationManager()->get();
        $vehicles = Vehicle::get();
        return view('vehicle-to-manager.modals.create', compact('managers', 'vehicles'));
    }

    public function show(User $assign): View
    {
        echo "<pre>" . print_r($assign->toArray(), true) . "</pre>";
        return view('vehicle-to-manager.modals.show', compact('assign'));
    }


    // DataTable for vehicles assign
    public function dataTable(Request $request): JsonResponse
    {
        // print_r($request->all());
        $dt = DataTables::of(VehicleAssign::with(['vehicle', 'preparationManager'])->latest());
        // echo  "<pre>" . print_r($dt->toArray(), true);

        $dt->filter(function ($query) use ($request) {
            if (empty($request->input('search'))) return;

            $search = trim($request->input('search')['value']);
            $keywords = explode(' ', $search);

            $query->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->orWhereHas('vehicle', function ($q) use ($word) {
                        $q->where('model', 'like', "%$word%")
                            ->orWhere('make', 'like', "%$word%")
                            ->orWhere('year', 'like', "%$word%");
                    })
                        ->orWhereHas('preparationManager', function ($q) use ($word) {
                            $q->where('name', 'like', "%$word%")
                                ->orWhere('email', 'like', "%$word%");
                        });

                }
            });
        });

        // Add manager column for super admin
        if (auth()->user()->isSuperAdmin()) {
            // $dt->addColumn('manager', function ($record) {
            //     if (!$record->preparationManager) return '-';

            //     return '<div class="user-card">
            //     <div class="user-avatar ' . getRandomColorClass() . '">
            //         ' . getAvatarHtml($record->preparationManager) . '
            //     </div>
            //     <div class="user-info">
            //         <span class="tb-lead">' . $record->preparationManager->name . '</span>
            //         <span>' . $record->preparationManager->email . '</span>
            //     </div>
            // </div>';
            // });
        }

        $dt->addColumn('preparation_manager', function ($record) {
            if (!$record->preparationManager) return '-';

            return '<div class="user-card">
            <div class="user-avatar ' . getRandomColorClass() . '">
                ' . getAvatarHtml($record->preparationManager) . '
            </div>
            <div class="user-info">
                <span class="tb-lead">' . $record->preparationManager->name . '</span>
                <span>' . $record->preparationManager->email . '</span>
            </div>
        </div>';
        });

        $dt->addColumn('vehicle', function ($record) {
            if (!$record->vehicle) return '-';

            return '<div class="d-flex align-items-center">
            <div class="me-2">
                <em class="icon ni ni-truck fs-4"></em>
            </div>
            <div>
                <span class="tb-lead">' . $record->vehicle->model . '</span>
                <div class="small text-muted">' . $record->vehicle->make . ' · ' . $record->vehicle->registration . '</div>
            </div>
        </div>';
        });

        // Actions column
        $dt->addColumn('actions', function ($record) {
            $links = [
                ['action' => 'view', 'modalSize' => 'lg'],
                ['action' => 'delete'],
            ];

            return (new DataTableActionLinksService(
                model: $record,
                routeNamespace: 'vehicles-assign',
                datatableId: '#vehicles-assign-dt'
            ))->byArray($links);
        });

        $dt->addIndexColumn();

        $dt->rawColumns(['actions', 'preparation_manager', 'vehicle']);

        return $dt->make(true);
    }
}
