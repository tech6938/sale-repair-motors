<?php

namespace App\Http\Controllers\Profile;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display the profile index view.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('profile.index');
    }

    /**
     * Show the form for editing the user's profile information.
     *
     * @return \Illuminate\View\View
     */
    public function edit(): View
    {
        return view('profile.modals.profile');
    }

    /**
     * Update the authenticated user's profile information.
     *
     * Validates the request data and updates the user's profile fields
     * including name, avatar, phone, and address. If an avatar file is
     * provided, it is uploaded and the path is updated.
     *
     * @param  \App\Http\Requests\Profile\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $only = [
                'name',
                'phone',
                'address',
            ];

            auth()->user()->update($request->only($only));

            $params = [];

            foreach ($only as $field) {
                $params[$field] = auth()->user()?->$field;
            }

            $params['name'] = auth()->user()->name;

            DB::commit();

            return $this->jsonResponse([
                'message' => 'Profile updated successfully.',
                'params' => $params
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }
}
