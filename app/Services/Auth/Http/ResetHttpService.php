<?php

namespace App\Services\Auth\Http;

use App\Events\Auth\PasswordChangedEvent;
use App\Models\User;
use App\Services\Auth\Dto\ResetPasswordDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetHttpService
{
    public function reset(ResetPasswordDto $dto): array
    {
        $code = DB::table('password_resets')
            ->where('code', '=', $dto->code)
            ->first();

        $time = Carbon::create($code->expired_at);

        if (empty($code) || ($time->diffInMinutes(now()) > 1)) {
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
