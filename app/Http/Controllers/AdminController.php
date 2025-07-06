<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\DataTableActionLinksService;
use App\Http\Requests\Admins\AdminStoreRequest;
use App\Http\Requests\Admins\AdminUpdateRequest;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('admins.index');
    }

    /**
     * Display the create admin modal.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('admins.modals.create');
    }

    /**
     * Store a newly created admin in storage.
     *
     * Validates the request data, creates a new admin record, assigns the admin role to the user,
     * and returns a success response. If the request fails, it rolls back the transaction and
     * returns the error message.
     *
     * @param AdminStoreRequest $request
     * @return JsonResponse
     */
    public function store(AdminStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $admin = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);

            $admin->assignRole(User::ROLE_ADMIN);

            DB::commit();

            return $this->jsonResponse(['message' => 'Admin has been created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $admin
     * @return \Illuminate\View\View
     */
    public function show(User $admin): View
    {
        return view('admins.modals.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\User $admin
     * @return \Illuminate\View\View
     */
    public function edit(User $admin): View
    {
        return view('admins.modals.edit', compact('admin'));
    }

    /**
     * Update the specified admin's status and comments.
     *
     * Validates the request to ensure the status and comments are provided correctly.
     * Updates the admin's status based on the input and sets admin comments if necessary.
     * Rolls back the transaction in case of an error and returns an appropriate response.
     *
     * @param \App\Http\Requests\Admins\AdminUpdateRequest $request
     * @param \App\Models\User $admin
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AdminUpdateRequest $request, User $admin): JsonResponse
    {
        try {
            DB::beginTransaction();

            $admin->update([
                'status' => empty($request->input('status')) ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE,
                'admin_comments' => empty($request->input('status')) ? $request->input('comments') : null
            ]);

            DB::commit();

            return $this->jsonResponse(['message' => 'Admin updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified admin from storage.
     *
     * Deletes the admin from the database and returns a success response.
     * Rolls back the transaction in case of an error and returns an appropriate response.
     *
     * @param \App\Models\User $admin
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $admin)
    {
        try {
            DB::beginTransaction();

            $admin->delete();

            DB::commit();

            return $this->jsonResponse(['message' => 'Admin removed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the comments for the specified admin.
     *
     * @param \App\Models\User $admin
     * @return \Illuminate\View\View
     */
    public function comments(User $admin): View
    {
        return view('admins.modals.comments', compact('admin'));
    }

    /**
     * Return the listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataTable(Request $request): JsonResponse
    {
        $dt = DataTables::of(User::applyRoleFilter()->admin()->with('manager')->latest());

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
                            <span class="tb-lead">
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
                            <span class="tb-lead">
                                <a href="' . route('admins.show', $record->uuid) . '" async-modal async-modal-size="lg">
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
            return empty($record->admin_comments)
                ? canEmpty(null)
                : '<a href="' . route('admins.comments', $record->uuid) . '" class="btn btn-icon btn-sm btn-light" async-modal
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
                routeNamespace: 'admins',
                datatableId: '#admins-dt',
                isLocked: $record->id == auth()->user()->id
            ))->byArray($links);
        });

        $dt->addIndexColumn();

        $dt->rawColumns(['actions', 'manager', 'name', 'phone', 'address', 'status', 'comments', 'created', 'updated']);

        return $dt->make(true);
    }
}
