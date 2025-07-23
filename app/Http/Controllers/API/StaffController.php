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

class StaffController extends BaseController implements HasMiddleware
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
     */
    public function list()
    {
        return $staffs = User::applyRoleFilter()
            ->staff()
            ->applyRequestFilters()
            ->paginate(request()->input('page_size', $this->perPage));

        return $this->apiResponse(
            'Staffs list fetched successfully.',
            JsonResponse::HTTP_OK,
            new UserCollection($staffs)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $staff)
    {
        return $this->apiResponse(
            'Staff details fetched successfully.',
            JsonResponse::HTTP_OK,
            new UserResource($staff)
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

        $staff = $staff->assignRole(User::ROLE_STAFF);

        DB::commit();

        return $this->apiResponse(
            'Staff created successfully.',
            JsonResponse::HTTP_OK,
            new UserResource($staff)
        );
    }

    /**
     * Update the specified admin's suspension status and comments.
     *
     * Validates the request to ensure the suspension status and comments are provided correctly.
     * Updates the admin's status based on the suspension input and sets admin comments if suspended.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $staff)
    {
        $request->validate([
            'is_suspended' => 'required|in:yes,no',
            'admin_comments' => 'required_if:is_suspended,yes|nullable|string|min:1|max:1000',
        ], [
            'is_suspended.in' => 'Provided suspending status is invalid. It must be either "yes" or "no".',
            'admin_comments.required_if' => 'Please provide a comment for suspending the admin.',
        ]);

        $staff->update([
            'status' => $request->input('is_suspended') === 'yes' ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE,
            'admin_comments' => $request->input('is_suspended') === 'yes' ? $request->input('admin_comments') : null
        ]);

        return $this->apiResponse(
            'Staff updated successfully.',
            JsonResponse::HTTP_OK,
            new UserResource($staff)
        );
    }

    /**
     * Remove the specified staff from storage.
     *
     * Deletes the staff from the database and returns a success response.
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $staff)
    {
        $staff->delete();

        return $this->apiResponse(
            'Staff deleted successfully.',
            JsonResponse::HTTP_OK,
        );
    }
}
