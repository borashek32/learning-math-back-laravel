<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Http\Resources\v1\Auth\LoginResource;
use App\Http\Resources\v1\Auth\RegisterResource;
use App\Mail\RegisterMail;
use App\Traits\HttpResponses\HttpResponses;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class AuthController extends Controller
{
    use HttpResponses;

    public function register(RegisterRequest $request)
    {
        if (!$request->exists(['email', 'password', 'password_confirmation'])) {
            response()->json([
                'data' => [
                    'message' => 'Something went wrong',
                ],
            ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = $this->viaEmail($request->email, $request->password);

        $cookie = cookie(
            name: 'access_token',
            value: $response['access_token'],
            minutes: 60 * 24,
            domain: '.' . str_replace(['https://', 'http://'], '', config('app.url'))
        );

        return (new RegisterResource($response))
            ->response()
            ->withCookie($cookie)
            ->setStatusCode(StatusCode::HTTP_CREATED);
    }

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

    public function logout()
    {

    }
}
