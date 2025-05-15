<?php

namespace App\Http\Controllers\API\Profile;

use App\Models\User;
use App\Traits\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User\UserResource;
use App\Http\Controllers\API\BaseController;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProfileController extends BaseController implements HasMiddleware
{
    use FileUploader;

    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::getRoles(separator: '|'), only: ['view', 'update']),
        ];
    }

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

        DB::beginTransaction();

        auth()->user()->update([
            'name' => $request->input('name'),
            'avatar' => $path,
            'phone' => $request->input('phone', auth()->user()->phone),
            'address' => $request->input('address'),
        ]);

        DB::commit();

        return $this->apiResponse(
            'Profile updated successfully.',
            JsonResponse::HTTP_OK,
            new UserResource(auth()->user())
        );
    }
}
