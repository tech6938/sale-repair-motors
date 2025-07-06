<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    private $allowedKeys = [
        'is_compact_sidebar',
        'is_dark_mode',
    ];

    /**
     * Store user settings in the database.
     *
     * Iterates over a predefined list of allowed setting keys. For each key that is present
     * in the request, it updates or creates a new setting entry for the authenticated user.
     * If the process is successful, a success response is returned. In case of an exception, 
     * the transaction is rolled back and an error response is returned.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            foreach ($this->allowedKeys as $key) {
                if (!$request->has($key)) continue;

                auth()->user()->settings()->updateOrCreate([
                    'key' => $key,
                ], [
                    'value' => $request->input($key) ?? false
                ]);
            }

            DB::commit();

            return $this->jsonResponse('Settings saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse($e->getMessage(), $e->getCode());
        }
    }
}
