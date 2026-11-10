<?php

declare(strict_types=1);

namespace App\Traits\HttpResponses;

use Illuminate\Http\JsonResponse;

trait HttpResponses
{
    protected function success(mixed $data, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Request was successful',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(mixed $data, string $message, int $code): JsonResponse
    {
        return response()->json([
            'status' => 'Error occurred',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
