<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Return JSON/Async response for web app
     *
     * @return JsonResponse
     */
    public function jsonResponse($data, $status = 200): JsonResponse
    {
        $status = is_int($status) && $status > 99 ? $status : 500;

        if (is_object($data) && method_exists($data, 'errors')) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $data->errors()
            ], $status);
        }

        if (is_array($data) && !($status >= 200 && $status < 300)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $data
            ], $status);
        }

        return response()->json($data, $status);
    }
}
