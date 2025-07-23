<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User\UserResource;
use Illuminate\Validation\Rules\Password;
use App\Http\Resources\User\UserCollection;
use App\Http\Controllers\API\BaseController;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ManagerStaffController extends BaseController implements HasMiddleware
{
    /**
     * Specify the middleware that is used by this controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::ROLE_ADMIN),
        ];
    }

    /**
     * Get a list of staffs.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     * */

    public function list()
    {
        return $staffs = User::managerStaff()
            ->applyRoleFilter()
            ->applyRequestFilters()
            ->paginate(request()->input('page_size', $this->perPage));

        return $this->apiResponse(
            'Manager Staff list fetched successfully.',
            JsonResponse::HTTP_OK,
            new UserCollection($staffs)
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::beginTransaction();

        $staff = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        $staff = $staff->assignRole(User::ROLE_ADMIN_STAFF);

        DB::commit();

        return $this->apiResponse(
            'Staff created successfully.',
            JsonResponse::HTTP_OK,
            new UserResource($staff)
        );
    }
}
