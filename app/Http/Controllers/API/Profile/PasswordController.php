<?php

namespace App\Http\Controllers\API\Profile;

use App\Models\User;
use App\Traits\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PasswordController extends BaseController implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('role:' . User::getRoles(separator: '|'), only: ['view', 'update']),
        ];
    }

    /**
     * Update logged in user profile
     *
     * @param Request $request
     */
    public function change(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|string|min:8|max:128|confirmed',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), JsonResponse::HTTP_BAD_REQUEST);
        }

        if (! Hash::check($request->input('old_password'), auth()->user()->password)) {
            throw new \Exception('The provided old password is incorrect.');
        }

        auth()->user()->update([
            'password' => $request->input('password'),
        ]);

        return $this->apiResponse(
            'Password updated successfully.',
            JsonResponse::HTTP_OK,
        );
    }
}
