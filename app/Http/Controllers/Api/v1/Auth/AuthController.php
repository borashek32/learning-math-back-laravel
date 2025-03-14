<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Mail\RegisterMail;
use App\Traits\HttpResponses\HttpResponses;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use HttpResponses;

    public function register(RegisterRequest $request)
    {
        $request->validated($request->all());

        Mail::to('borashek@inbox.ru')->send(new RegisterMail());

        $user = User::query()->create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_confirmation' => Hash::make($request->password_confirmation),
        ]);



        return $this->success([
            'message' => 'User registered successfully.',
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password')))
            {
                return $this->error('', 'Unauthorized', 401);
            }

        $user = User::query()->where('email', '=', $request->email)->first();

        return $this->success([
            'message' => 'User logged in successfully.',
            'user' => $user,
        ]);
    }

    public function logout()
    {

    }
}
