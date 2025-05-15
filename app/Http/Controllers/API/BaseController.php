<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    public int $perPage = 10;

    /**
     * Return API raw response
     *
     * @return void
     */
    public function apiResponse($message, $statusCode, $data = []): JsonResponse
    {
        $statusCode = is_int($statusCode) && $statusCode > 99 ? $statusCode : 500;

        return response()->json([
            'success' => $statusCode < 400,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
