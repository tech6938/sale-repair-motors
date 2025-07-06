<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use Illuminate\Validation\UnauthorizedException;

class LoginController extends BaseController
{
    /**
     * Login a user and return a token that can be used to authenticate the user on subsequent requests.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
            'fcm_token' => 'required|string|max:255',
        ], [
            'email.exists' => 'These credentials do not match our records.',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = User::whereEmail($request->input('email'))->notSuperAdmin()->firstOrFail();

        // We can handle these checks manually here since we don't have an auth user at this point
        if ($user->isSuspended()) {
            throw new UnauthorizedException('It looks like your account has been terminated for an indefinite period.');
        }

        if (!auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            throw new UnauthorizedException('These credentials do not match our records.');
        }

        $user->updateFcmToken($request->input('fcm_token'));

        return $this->apiResponse(
            'You are logged in successfully.',
            JsonResponse::HTTP_OK,
            [
                'user' => new UserResource(auth()->user()),
                'token' => auth()->user()->createToken(auth()->user()->email)->plainTextToken,
            ]
        );
    }

    /**
     * Log out the user from the application.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->apiResponse('You are logged out successfully.', JsonResponse::HTTP_OK);
    }
}
