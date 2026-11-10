<?php

namespace App\Services\Auth\Http;

use App\Events\Auth\PasswordRecoveryRequestedEvent;
use App\Models\User;
use App\Services\Auth\Dto\RecoveryPasswordDto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecoveryHttpService
{
    public function viaEmail(RecoveryPasswordDto $dto): array
    {

        $user = User::query()->where('email', '=', $dto->email)->first();

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found.',
            ];
        }

        $code = Str::random(10);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'code' => $code,
            'expired_at' => now()->addHour(),
            'created_at' => now(),
        ]);

        event(new PasswordRecoveryRequestedEvent($user->email, $code));

        return [
            'status' => true,
            'message' => 'Auth recovery link sent successfully.',
        ];

    }
}
