<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __construct(#[CurrentUser] private readonly User $user) {}

    public function logout(): JsonResponse
    {
        $this->user->currentAccessToken()->delete();

        return response()->json([
            'data' => [
                'message' => 'Logged out successfully',
            ],
        ]);
    }

    public function logoutFromAllDevices(): JsonResponse
    {
        $this->user->tokens()->delete();

        return response()->json([
            'data' => [
                'message' => 'Logged out successfully',
            ],
        ]);
    }
}
