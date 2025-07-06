<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User\UserResource;
use Illuminate\Validation\Rules\Password;
use App\Http\Resources\User\UserCollection;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminController extends BaseController implements HasMiddleware
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
     * List all admins
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $admins = User::applyRoleFilter()
            ->admin()
            ->applyRequestFilters()
            ->paginate(request()->input('page_size', $this->perPage));

        return $this->apiResponse(
            'Admins list fetched successfully.',
            JsonResponse::HTTP_OK,
            new UserCollection($admins)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param App\Models\User $admin
     * @return Illuminate\Http\JsonResponse
     */
    public function show(User $admin)
    {
        return $this->apiResponse(
            'Admin details fetched successfully.',
            JsonResponse::HTTP_OK,
            new UserResource($admin)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::beginTransaction();

        $admin = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        $admin->assignRole(User::ROLE_ADMIN);

        DB::commit();

        return $this->apiResponse(
            'Admin created successfully.',
            JsonResponse::HTTP_OK,
            new UserResource($admin)
        );
    }

    /**
     * Update the specified admin's suspension status and comments.
     *
     * Validates the request to ensure the suspension status and comments are provided correctly.
     * Updates the admin's status based on the suspension input and sets admin comments if suspended.
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\User $admin
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $admin)
    {
        $request->validate([
            'is_suspended' => 'required|in:yes,no',
            'admin_comments' => 'required_if:is_suspended,yes|nullable|string|min:1|max:1000',
        ], [
            'is_suspended.in' => 'Provided suspending status is invalid. It must be either "yes" or "no".',
            'admin_comments.required_if' => 'Please provide a comment for suspending the admin.',
        ]);

        $admin->update([
            'status' => $request->input('is_suspended') === 'yes' ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE,
            'admin_comments' => $request->input('is_suspended') === 'yes' ? $request->input('admin_comments') : null
        ]);

        return $this->apiResponse(
            'Admin updated successfully.',
            JsonResponse::HTTP_OK,
            new UserResource($admin)
        );
    }

    /**
     * Remove the specified admin from storage.
     *
     * Deletes the admin from the database and returns a success response.
     *
     * @param App\Models\User $admin
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(User $admin)
    {
        $admin->delete();

        return $this->apiResponse(
            'Admin deleted successfully.',
            JsonResponse::HTTP_OK,
        );
    }
}
