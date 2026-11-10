<?php

namespace App\Traits\HttpResponses;

trait HttpResponses
{
    protected function success($data, $message = null, $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'Request was successful',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($data, $message, $code): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'Error occurred',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
