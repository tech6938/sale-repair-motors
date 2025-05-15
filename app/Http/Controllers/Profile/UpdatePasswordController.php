<?php

namespace App\Http\Controllers\Profile;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Profile\PasswordUpdateRequest;

class UpdatePasswordController extends Controller
{
    public function edit(): View
    {
        return view('profile.modals.password');
    }

    public function update(PasswordUpdateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            if (!(Hash::check($request->get('old_password'), auth()->user()->password))) {
                return $this->jsonResponse([
                    'old_password' => ['The provided old password is incorrect.']
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            auth()->user()->update([
                'password' => $request->input('password'),
            ]);

            DB::commit();

            return $this->jsonResponse('Password updated successfully.', JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }
}
