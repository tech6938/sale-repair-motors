<?php

namespace App\Http\Controllers\API\Profile;

use App\Traits\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\User\UserResource;
use App\Http\Controllers\API\BaseController;

class ProfileController extends BaseController
{
    use FileUploader;

    /**
     * Return logged in user profile details
     */
    public function view()
    {
        return $this->apiResponse(
            'Profile details fetched successfully.',
            JsonResponse::HTTP_OK,
            new UserResource(auth()->user())
        );
    }

    /**
     * Update logged in user profile
     *
     * @param Request $request
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'avatar' => 'nullable|file|mimes:jpg,png,gif|max:10000', // 10000 KB ~= 10 MB
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $path = auth()->user()->avatar;

        if ($request->hasFile('avatar')) {
            $path = $this->uploadPublicImage(
                $request->file('avatar'),
                'avatars',
                auth()->user()->avatar
            );
        }

        auth()->user()->update([
            'name' => $request->input('name'),
            'avatar' => $path,
            'phone' => $request->input('phone', auth()->user()->phone),
            'address' => $request->input('address'),
        ]);

        return $this->apiResponse(
            'Profile updated successfully.',
            JsonResponse::HTTP_OK,
            new UserResource(auth()->user())
        );
    }
}
