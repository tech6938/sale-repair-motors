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
    public function index(): View
    {
        return view('profile.index');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        return view('profile.modals.profile');
    }

    /**
     * Update the user's profile information.
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

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        auth()->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
