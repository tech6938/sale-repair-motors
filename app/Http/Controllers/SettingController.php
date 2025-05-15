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
     * Store a newly created resource in storage.
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
