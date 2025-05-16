<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use NjoguAmos\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use Illuminate\Validation\UnauthorizedException;

class ForgotPasswordController extends BaseController
{
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'We cannot find the email address in our system.'
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = User::whereEmail($request->input('email'))->notSuperAdmin()->firstOrFail();

        if (! $user->hasVerifiedEmail()) {
            throw new \Exception('Your email address is not verified.');
        }

        if ($user->isSuspended()) {
            throw new UnauthorizedException('It looks like your account has been terminated for an indefinite period.');
        }

        $user->sendOtpEmailNotification(
            Otp::generate(identifier: $request->input('email'))
        );

        return $this->apiResponse(
            'An OTP has been sent to your email.',
            JsonResponse::HTTP_OK,
        );
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:50|exists:users,email',
            'password' => 'required|string|min:8|max:128|confirmed',
            'otp' => 'required|string|size:' . config('otp.length'),
        ], [
            'email.exists' => 'We cannot find the email address in our system.'
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = User::whereEmail($request->input('email'))->notSuperAdmin()->firstOrFail();

        if (! $user->hasVerifiedEmail()) {
            throw new \Exception('Your email address is not verified.');
        }

        if ($user->isSuspended()) {
            throw new UnauthorizedException('It looks like your account has been terminated for an indefinite period.');
        }

        $isValidated = Otp::validate(identifier: $user->email, token: $request->input('otp'));

        if (! $isValidated) {
            throw new \Exception('Unable to validate the provided OTP.');
        }

        $user->update([
            'password' => $request->input('password'),
        ]);

        return $this->apiResponse(
            'Password has been updated successfully.',
            JsonResponse::HTTP_OK,
        );
    }
}
