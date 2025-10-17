<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class LogoutController extends Controller
{
    public function __construct(#[CurrentUser] private readonly User $user) {}

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Выход из текущей сессии",
     *     description="Удаляет текущий токен доступа (logout из текущего устройства)",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Успешный выход",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Logged out successfully")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован (токен отсутствует или неверен)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        $this->user->currentAccessToken()->delete();

        return response()->json([
            'data' => [
                'message' => 'Logged out successfully',
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout/all",
     *     summary="Выход со всех устройств",
     *     description="Удаляет все активные токены пользователя (logout со всех устройств)",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Успешный выход со всех устройств",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Logged out successfully")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован (токен отсутствует или неверен)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
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
