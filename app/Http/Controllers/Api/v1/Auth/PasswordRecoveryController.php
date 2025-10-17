<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\PasswordRecoveryRequest;
use App\Http\Requests\v1\Auth\PasswordResetRequest;
use App\Services\Auth\Dto\RecoveryPasswordDto;
use App\Services\Auth\Dto\ResetPasswordDto;
use App\Services\Auth\Http\RecoveryHttpService;
use App\Services\Auth\Http\ResetHttpService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class PasswordRecoveryController extends Controller
{
    public function __construct(
        protected RecoveryHttpService $recoveryService,
        protected ResetHttpService $resetService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/password/recovery",
     *     summary="Отправить код восстановления пароля",
     *     description="Отправляет на email пользователя код для восстановления пароля.",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Код успешно отправлен",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Recovery code sent successfully.")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="User not found.")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Something went wrong")
     *             )
     *         )
     *     )
     * )
     */
    public function sendRecoveryCode(PasswordRecoveryRequest $request): JsonResponse
    {
        if ($request->exists(['email'])) {
            $dto = new RecoveryPasswordDto([
                'email' => $request->get('email'),
            ]);

            $response = $this->recoveryService->viaEmail($dto);

            return response()->json($response, ($response['status']) ? StatusCode::HTTP_OK : StatusCode::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => [
                'message' => 'Something went wrong',
            ],
        ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/password/reset",
     *     summary="Сбросить пароль пользователя",
     *     description="Позволяет пользователю установить новый пароль, используя код восстановления.",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "password"},
     *             @OA\Property(property="code", type="string", example="123456"),
     *             @OA\Property(property="password", type="string", format="password", example="newStrongPassword123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Пароль успешно изменён",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Password successfully reset.")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Неверный код восстановления или истёк срок действия",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid or expired recovery code.")
     *             )
     *         )
     *     )
     * )
     */
    public function reset(PasswordResetRequest $request)
    {
        $dto = new ResetPasswordDto([
            'password' => $request->get('password'),
            'code' => $request->get('code'),
        ]);

        $response = $this->resetService->reset($dto);

        return response()->json($response, ($response['status']) ? StatusCode::HTTP_OK : StatusCode::HTTP_FORBIDDEN);
    }
}
