<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\DataTableActionLinksService;
use App\Http\Requests\Staffs\StaffStoreRequest;
use App\Http\Requests\Staffs\StaffUpdateRequest;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('staffs.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('staffs.modals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaffStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $staff = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);

            $staff->assignRole(User::ROLE_STAFF);

            DB::commit();

            return $this->jsonResponse(['message' => 'Staff has been created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $staff): View
    {
        return view('staffs.modals.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $staff): View
    {
        return view('staffs.modals.edit', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StaffUpdateRequest $request, User $staff): JsonResponse
    {
        try {
            DB::beginTransaction();

            $staff->update([
                'status' => empty($request->input('status')) ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE,
                'staff_comments' => empty($request->input('status')) ? $request->input('comments') : null
            ]);

            DB::commit();

            return $this->jsonResponse(['message' => 'Staff updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $staff)
    {
        try {
            DB::beginTransaction();

            $staff->delete();

            DB::commit();

            return $this->jsonResponse(['message' => 'Staff removed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Show staff comments for the resource.
     */
    public function comments(User $staff): View
    {
        return view('staffs.modals.comments', compact('staff'));
    }

    /**
     * Return the listing of the resource.
     */
    public function dataTable(Request $request): JsonResponse
    {
        $dt = DataTables::of(User::applyRoleFilter()->staff()->with('manager')->latest());

        $dt->filter(function ($query) use ($request) {
            if (empty($request->input('search'))) return;

            $search = trim($request->input('search')['value']);
            $keywords = explode(' ', $search);

            $query->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->orWhere('name', 'like', "%$word%")
                        ->orWhere('email', 'like', "%$word%")
                        ->orWhere('phone', 'like', "%$word%")
                        ->orWhere('address', 'like', "%$word%")
                        ->orWhere('status', 'like', "%$word%");
                }
            });
        });

        if (auth()->user()->isSuperAdmin()) {
            $dt->addColumn('manager', function ($record) {
                if ($record->manager->id == auth()->user()->id) {
                    return '<div class="user-card">
                                <div class="user-avatar ' . getRandomColorClass() . '">
                                    ' . getAvatarHtml($record->manager) . '
                                </div>
                                <div class="user-info">
                                    <span class="tb-lead">' . $record->manager->name . '</span>
                                    <span>' . $record->manager->email . '</span>
                                </div>
                            </div>';
                }

                return '<div class="user-card">
                        <div class="user-avatar ' . getRandomColorClass() . ' d-none d-sm-flex">
                            ' . getAvatarHtml($record->manager) . '
                        </div>
                        <div class="user-info">
                            <span class="tb-lead text-danger">
                                <a href="' . route('admins.show', $record->manager->uuid) . '" async-modal async-modal-size="lg">
                                    ' . $record->manager->name . '
                                </a>
                            </span>
                            <span>' . $record->manager->email . '</span>
                        </div>
                    </div>';
            });
        }

        $dt->addColumn('name', function ($record) {
            return '<div class="user-card">
                        <div class="user-avatar ' . getRandomColorClass() . ' d-none d-sm-flex">
                            ' . getAvatarHtml($record) . '
                        </div>
                        <div class="user-info">
                            <span class="tb-lead text-danger">
                                <a href="' . route('staffs.show', $record->uuid) . '" async-modal async-modal-size="lg">
                                    ' . $record->name . '
                                </a>
                            </span>
                            <span>' . $record->email . '</span>
                        </div>
                    </div>';
        });

        $dt->addColumn('phone', function ($record) {
            return canEmpty($record->phone);
        });

        $dt->addColumn('address', function ($record) {
            return $record->address ? addEllipsis(ucfirst($record->address)) : canEmpty($record->address);
        });

        $dt->addColumn('status', function ($record) {
            return $record->status_badge;
        });

        $dt->addColumn('comments', function ($record) {
            return empty($record->staff_comments)
                ? canEmpty(null)
                : '<a href="' . route('staffs.comments', $record->uuid) . '" class="btn btn-icon btn-sm btn-light" async-modal
                        data-bs-toggle="tooltip" title="View Comments" data-method="post">
                        <em class="icon ni ni-comments"></em>
                   </a>';
        });

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
                ['action' => 'delete', 'shouldRender' => $record->id !== auth()->user()->id],
            ];

            return (new DataTableActionLinksService(
                model: $record,
                routeNamespace: 'staffs',
                datatableId: '#staffs-dt',
                isLocked: $record->id == auth()->user()->id
            ))->byArray($links);
        });

        $dt->addIndexColumn();

        $dt->rawColumns(['actions', 'manager', 'name', 'phone', 'address', 'status', 'comments', 'created', 'updated']);

        return $dt->make(true);
    }
}
