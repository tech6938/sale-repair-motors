<?php

namespace App\Http\Controllers\Profile;

use Illuminate\View\View;
use App\Traits\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AvatarController extends Controller
{
    use FileUploader;

    /**
     * Show the form for updating the authenticated user's avatar.
     *
     * @return \Illuminate\View\View
     */
    public function edit(): View
    {
        return view('profile.modals.avatar');
    }

    /**
     * Update the authenticated user's avatar.
     *
     * Validates the request data and updates the user's avatar. If an avatar file is 
     * provided, it is uploaded and the path is updated.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|file|mimes:jpg,png,gif|max:512', // 512 KB = 0.5 MB
            ], [
                'avatar.size' => 'Avatar must be less then 1 MB.'
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $path = $this->uploadPublicImage(
                $request->file('avatar'),
                'avatars',
                auth()->user()->avatar
            );

            auth()->user()->update([
                'avatar' => $path,
            ]);

            return $this->jsonResponse([
                'message' => 'Avatar updated successfully.',
                'params' => [
                    'url' => Storage::url($path)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }
}
