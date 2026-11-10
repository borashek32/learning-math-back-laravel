<?php

declare(strict_types=1);

namespace App\Services\Auth\Http;

use App\Events\Auth\PasswordChangedEvent;
use App\Models\User;
use App\Services\Auth\Dto\ResetPasswordDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetHttpService
{
    /**
     * @return array{status: bool, message: string}
     */
    public function reset(ResetPasswordDto $dto): array
    {
        $code = DB::table('password_resets')
            ->where('code', '=', $dto->code)
            ->first();

        if ($code === null) {
            return [
                'status' => false,
                'message' => 'Code not found',
            ];
        }

        $expiresAt = Carbon::parse($code->expired_at);

        if ($expiresAt->isPast()) {
            return [
                'status' => false,
                'message' => 'Code expired',
            ];
        }

        $user = User::query()->where('email', '=', $code->email)->first();

        $user->password = Hash::make($dto->password);
        $user->last_password_update_date = Carbon::now();
        $user->save();

        DB::table('password_resets')->delete($code->id);

        event(new PasswordChangedEvent($user->email));

        return [
            'status' => true,
            'message' => 'Auth changed',
        ];
    }
}
