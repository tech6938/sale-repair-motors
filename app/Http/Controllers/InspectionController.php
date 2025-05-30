<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Services\DataTableActionLinksService;

class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('inspections.index');
    }

    /**
     * Return the listing of the resource.
     */
    public function dataTable(Request $request): JsonResponse
    {
        $vehicles = Vehicle::applyRoleFilter()
            ->with('user', 'inspections');

        // dd($vehicles->get()->toArray());

        $dt = DataTables::of($vehicles);

        $dt->filter(function ($query) use ($request) {
            if (empty($request->input('search'))) return;

            $search = trim($request->input('search')['value']);
            $keywords = explode(' ', $search);

            $query->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    // $query->orWhere('name', 'like', "%$word%")
                    //     ->orWhere('email', 'like', "%$word%")
                    //     ->orWhere('phone', 'like', "%$word%")
                    //     ->orWhere('address', 'like', "%$word%")
                    //     ->orWhere('status', 'like', "%$word%");
                }
            });
        });

        $dt->addColumn('vehicle', function ($record) {
            return '<span class="tb-product">
                        <a href="' . getImageUrlByPath($record->image) . '" class="popup-image">
                            <img src="' . getImageUrlByPath($record->image, true) . '" class="thumb" onerror="_ie(this)">
                        </a>
                        <div class="user-info">
                            <span class="tb-lead">
                                <a href="' . route('inspections.show', $record->uuid) . '">
                                    ' . implode(' ', [$record->make, $record->model]) . '
                                </a>
                            </span>
                            <span>' . implode(' | ', [$record->year, $record->color]) . '</span>
                        </div>
                    </span>';
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

        $dt->addColumn('created', function ($record) {
            return $record->createdAt();
        });

        $dt->addColumn('updated', function ($record) {
            return $record->updatedAt();
        });

        $dt->addColumn('actions', function ($record) {
            $links = [
                ['action' => 'update'],
                ['action' => 'view', 'modalSize' => 'lg'],
                ['action' => 'delete', 'shouldRender' => true],
            ];

            return (new DataTableActionLinksService(
                model: $record,
                routeNamespace: 'admins',
                datatableId: '#admins-dt',
                isLocked: false
            ))->byArray($links);
        });

        $dt->addIndexColumn();

        $dt->rawColumns(['actions', 'vehicle', 'manager', 'created', 'updated']);

        return $dt->make(true);
    }
}
