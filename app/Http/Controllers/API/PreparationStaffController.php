<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ManagerAssign;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Staffs\StaffStoreRequest;
use App\Http\Resources\PreparationStaffResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\Preparation_Staff\PreparationStaffCollection;

class PreparationStaffController extends BaseController implements HasMiddleware
{
    /**
     * Specify the middleware that is used by this controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::ROLE_PREPARATION_MANAGER),
        ];
    }

    /**
     * Create a new preparation staff.
     *
     * @param StaffStoreRequest $request
     * @return JsonResponse
     */
    public function create_staff(StaffStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $staff = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            $staff->assignRole(User::ROLE_PREPARATION_STAFF);

            if (auth()->user()->isPreparationManager()) {
                ManagerAssign::create([
                    'manager_id' => auth()->id(),
                    'staff_id' => $staff->id,
                ]);
            }

            DB::commit();

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Preparation Staff has been created successfully.',
                'data' => $staff,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }

    public function all_staff(Request $request)
    {
        $staffs = ManagerAssign::with('staff')->where('manager_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('page_size', $this->perPage));

        return $this->apiResponse(
            'Preparation Staffs fetched successfully.',
            JsonResponse::HTTP_OK,
            new PreparationStaffCollection($staffs)
        );
    }
}
