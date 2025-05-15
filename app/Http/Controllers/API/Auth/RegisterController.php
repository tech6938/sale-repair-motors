<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use NjoguAmos\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|max:100|email|unique:users,email',
            'password' => 'required|string|min:8|max:128|confirmed',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'role' => 'required|string|in:' . User::getRoles(separator: ','),
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), JsonResponse::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'status' => User::STATUS_ACTIVE,
        ]);

        $user->assignRole($request->input('role'));

        $user->sendOtpEmailNotification(
            Otp::generate(identifier: $request->input('email'))
        );

        DB::commit();

        return $this->apiResponse(
            'Successfully registered! An OTP has been sent to your email.',
            JsonResponse::HTTP_OK,
            new UserResource($user)
        );
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:50|exists:users,email',
        ], [
            'email.exists' => 'We cannot find the email address in our system.',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = User::whereEmail($request->input('email'))->whereNotAdmin()->firstOrFail();

        $user->sendOtpEmailNotification(
            Otp::generate(identifier: $user->email)
        );

        return $this->apiResponse(
            'A new OTP has been sent successfully to your email.',
            JsonResponse::HTTP_OK,
        );
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:50|exists:users,email',
            'otp' => 'required|string|size:' . config('otp.length'),
        ], [
            'email.exists' => 'We cannot find the email address in our system.',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = User::whereEmail($request->input('email'))->whereNotAdmin()->firstOrFail();

        $isValidated = Otp::validate(identifier: $user->email, token: $request->input('otp'));

        if (! $isValidated) {
            throw new \Exception('Unable to validate the provided OTP.');
        }

        $user->markEmailAsVerified();

        return $this->apiResponse(
            'Email has been verified successfully.',
            JsonResponse::HTTP_OK,
        );
    }
}
