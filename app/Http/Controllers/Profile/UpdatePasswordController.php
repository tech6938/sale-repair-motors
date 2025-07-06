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
    /**
     * Show the form for editing the user's password.
     *
     * @return \Illuminate\View\View
     */
    public function edit(): View
    {
        return view('profile.modals.password');
    }

    /**
     * Update the authenticated user's password.
     *
     * Validates the request data and updates the user's password. If the provided
     * old password is incorrect, it will return a 422 HTTP status code with an
     * error message. If the request is invalid, it will return a 400 HTTP status
     * code with the validation errors.
     *
     * @param  \App\Http\Requests\Profile\PasswordUpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
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
