<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordRecoveryControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    #[Test]
    public function sendRecoveryCodeSuccess(): void
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $user = User::query()->first();

        $result = $this->postJson('api/v1/password/recovery', [
            'email' => $user->email,
        ]);

        $result->assertStatus(200);
    }

    #[Test]
    public function sendRecoveryCodeWithInvalidEmail(): void
    {
        $this->postJson('api/v1/password/recovery', [
            'email' => 'invalid-email',
        ])->assertStatus(422);
    }

    #[Test]
    public function resetPasswordSuccess(): void
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $user = User::query()->first();
        $code = (string) $this->faker->randomNumber(6);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'code' => $code,
            'expired_at' => now()->addHour(),
        ]);

        $this->postJson('api/v1/password/reset', [
            'password' => 'passwordQ!1',
            'password_confirmation' => 'passwordQ!1',
            'code' => $code,
        ])->assertStatus(200);
    }

    #[Test]
    public function resetPasswordFail(): void
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $user = User::query()->first();
        $code = (string) $this->faker->randomNumber(6);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'code' => $code,
            'expired_at' => now()->subHour(),
        ]);

        $this->postJson('api/v1/password/reset', [
            'password' => 'passwordQ!1',
            'password_confirmation' => 'passwordQ!1',
            'code' => $code,
        ])->assertStatus(403);
    }

    #[Test]
    public function resetPasswordValidateError(): void
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $user = User::query()->first();
        $code = (string) $this->faker->randomNumber(6);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'code' => $code,
            'expired_at' => now()->subHour(),
        ]);

        $this->postJson('api/v1/password/reset', [
            'password' => 'passwordQ!1',
            'password_confirmation' => 'passwordQ!',
            'code' => $code,
        ])->assertStatus(422);
    }
}
