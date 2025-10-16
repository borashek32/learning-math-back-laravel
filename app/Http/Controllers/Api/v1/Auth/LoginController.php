<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Resources\v1\Auth\LoginResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Symfony\Component\HttpFoundation\Response as StatusCode;

class LoginController extends Controller
{
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
