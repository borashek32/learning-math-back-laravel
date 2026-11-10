<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Resources\v1\Auth\LoginResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Авторизация пользователя",
     *     description="Авторизация по email и паролю. Возвращает Bearer токен.",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Успешная авторизация",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string", example="1|ZQWERTYUIOPASDFGHJKL"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Неверный email или пароль",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid email or password")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка на сервере",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Something went wrong")
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        if ($request->exists(['email', 'password'])) {

            $check = $this->viaEmail($request->email, $request->password);

            if (!$check) {
                return response()->json([
                    'data' => [
                        'message' => 'Invalid email or password',
                    ],
                ], StatusCode::HTTP_UNAUTHORIZED);
            }

            return new LoginResource($check);
        }

        return response()->json([
            'data' => [
                'message' => 'Something went wrong',
            ],
        ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Проверяет пользователя по email и паролю.
     *
     * @return array|false
     */
    private function viaEmail(string $username, string $password)
    {
        $user = User::query()->where('email', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
