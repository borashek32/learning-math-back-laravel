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
use Symfony\Component\HttpFoundation\Response as StatusCode;

class RegisterController extends Controller
{
    public function __construct() {}

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
