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

class PreprationStaffController extends Controller
{
    /**
     * Displays the list of staffs.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('prepration-staff.index');
    }

    /**
     * Display the create staff modal.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('prepration-staff.modals.create');
    }

    /**
     * Store a newly created staff in storage.
     *
     * Validates the request data, creates a new staff record, assigns the staff role to the user,
     * and returns a success response. If the process fails, it rolls back the transaction and
     * returns the error message.
     *
     * @param StaffStoreRequest $request
     * @return JsonResponse
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

            $staff->assignRole(User::ROLE_PREPARATION_STAFF);

            DB::commit();

            return $this->jsonResponse(['message' => 'Prepration Manager has been created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the details of the specified staff.
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\View\View
     */
    public function show(User $staff): View
    {
        return view('prepration-staff.modals.show', compact('staff'));
    }

    /**
     * Displays the edit staff modal.
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\View\View
     */
    public function edit(User $staff): View
    {
        return view('prepration-staff.modals.edit', compact('staff'));
    }

    /**
     * Updates the specified staff in the database.
     *
     * Validates the request to ensure the suspension status and comments are provided correctly.
     * Updates the staff's status based on the suspension input and sets staff comments if suspended.
     * Rolls back the transaction in case of an error and returns an appropriate response.
     *
     * @param \App\Http\Requests\Staffs\StaffUpdateRequest $request
     * @param \App\Models\User $staff
     * @return \Illuminate\Http\JsonResponse
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

            return $this->jsonResponse(['message' => 'Prepration Manager updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Removes the specified staff from the database.
     *
     * Deletes the staff from the database and returns a success response.
     * Rolls back the transaction in case of an error and returns an appropriate response.
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $staff)
    {
        try {
            DB::beginTransaction();

            $staff->delete();

            DB::commit();

            return $this->jsonResponse(['message' => 'Prepration Manager removed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the comments for the specified staff.
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\View\View
     */
    public function comments(User $staff): View
    {
        return view('staffs.modals.comments', compact('staff'));
    }

    /**
     * Returns the staff datatable.
     *
     * The edit link is only visible if the logged in user is not the staff user.
     * The delete link is only visible if the logged in user is not the staff user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataTable(Request $request): JsonResponse
    {
        // dd($request->all());
        $dt = DataTables::of(User::applyRoleFilter()->PreprationStaff()->with('manager')->latest());

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
