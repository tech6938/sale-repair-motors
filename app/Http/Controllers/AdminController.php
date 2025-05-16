<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\DataTableActionLinksService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Admins\AdminStoreRequest;
use App\Http\Requests\Admins\AdminUpdateRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AdminController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::ROLE_SUPER_ADMIN),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admins.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admins.modals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $admin = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => getUuid(32),
                'status' => User::STATUS_INVITED,
                'updated_at' => null,
            ]);

            $admin->markEmailAsVerified();

            $admin->assignRole(User::ROLE_ADMIN);

            // Send the invite
            $admin->sendInvitationEmailNotification();

            DB::commit();

            return $this->jsonResponse(['message' => 'Invitation has been sent successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Resend invitation link in case of previously undelivered link
     */
    public function resendInvitation(User $admin)
    {
        try {
            if (!$admin->isInvited()) {
                throw new BadRequestException('Unable to resend the invitation. Try reloading the page.');
            }

            // Send the invite
            $admin->sendInvitationEmailNotification();

            return $this->jsonResponse(['message' => 'Invitation sent successfully.']);
        } catch (\Exception $e) {
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $admin): View
    {
        return view('admins.modals.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $admin): View
    {
        if (! $admin->hasCompleteProfile()) {
            throw new BadRequestException('Unable to update the specified resource.');
        }

        return view('admins.modals.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request, User $admin): JsonResponse
    {
        try {
            if (! $admin->hasCompleteProfile()) {
                throw new BadRequestException('Unable to update the specified resource.');
            }

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
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        try {
            if ($admin->id === auth()->user()->id) {
                throw new BadRequestException('Unable to delete the specified resource.');
            }

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
     * Show admin comments for the resource.
     */
    public function comments(User $admin): View
    {
        return view('admins.modals.comments', compact('admin'));
    }

    /**
     * Return the listing of the resource.
     */
    public function dataTable(Request $request): JsonResponse
    {
        $dt = DataTables::of(User::admin());

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

        $dt->addColumn('name', function ($record) {
            return '<div class="user-card">
                        <div class="user-avatar ' . getRandomColorClass() . ' d-none d-sm-flex">
                            ' . getAvatarHtml($record) . '
                        </div>
                        <div class="user-info">
                            <span class="tb-lead">' . $record->name . '</span>
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
                ['action' => 'update', 'shouldRender' => $record->hasCompleteProfile()],
                [
                    'action' => 'custom',
                    'shouldRender' => $record->isInvited(),
                    'url' => route('admins.resend-invitation', $record->uuid),
                    'attributes' => 'confirm-btn data-method="post" data-datatable="#admins-dt" data-message="Do you really want to resend invitation email"',
                    'icon' => 'reload',
                    'buttonText' => 'Resend Invitation',
                    'syncResponse' => true,
                ],
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

        $dt->rawColumns(['actions', 'name', 'phone', 'address', 'status', 'comments', 'created', 'updated']);

        return $dt->make(true);
    }
}
