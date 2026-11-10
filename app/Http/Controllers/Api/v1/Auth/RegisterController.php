<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Events\Auth\NewUserRegisteredEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Http\Resources\v1\Auth\RegisterResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class RegisterController extends Controller
{
    public function __construct() {}

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Регистрация нового пользователя",
     *     description="Создаёт нового пользователя и возвращает токен авторизации",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Пользователь успешно зарегистрирован",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             ),
     *             @OA\Property(property="access_token", type="string", example="1|ZQWERTYUIOPASDFGHJKL"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации (например, не совпадают пароли)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The email has already been taken.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка на сервере",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Something went wrong")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        if (!$request->exists(['email', 'password', 'password_confirmation'])) {
            return response()->json([
                'data' => [
                    'message' => 'Something went wrong',
                ],
            ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = $this->viaEmail($request->email, $request->password);

        event(new NewUserRegisteredEvent($response['user']['email']));

        $cookie = cookie(
            name: 'access_token',
            value: $response['access_token'],
            minutes: 60 * 24,
            domain: '.' . str_replace(['https://', 'http://'], '', config('app.url'))
        );

        return new RegisterResource($response)
            ->response()
            ->withCookie($cookie)
            ->setStatusCode(StatusCode::HTTP_CREATED);
    }

    private function viaEmail(string $email, string $password): array
    {
        $user = User::query()->create([
            'email' => $email,
            'password' => Hash::make($password),
            'last_password_update_date' => Carbon::now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'email' => $email,
        ];
    }
}
