<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PreparationStaffController extends Controller
{
    public function create_staff(StaffStoreRequest $request)
{
    try {
        DB::beginTransaction();

        // Create the user (staff)
        $staff = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Assign "Preparation Staff" role
        $staff->assignRole(User::ROLE_PREPARATION_STAFF);

        // Assign manager depending on who is creating the staff
        if (auth()->user()->isPreparationManager()) {
            ManagerAssign::create([
                'manager_id' => auth()->id(),
                'staff_id' => $staff->id,
            ]);
        } else {
            ManagerAssign::create([
                'manager_id' => $request->input('prepration_manager_id'),
                'staff_id' => $staff->id,
            ]);
        }

        DB::commit();

        return $this->jsonResponse([
            'message' => 'Preparation Staff has been created successfully.',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return $this->jsonResponse($e->getMessage(), $e->getCode());
    }
}

}
