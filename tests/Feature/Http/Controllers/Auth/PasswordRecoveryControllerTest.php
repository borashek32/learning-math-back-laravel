<?php

namespace Tests\Feature\app\Http\Controllers\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordRecoveryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function send_recovery_code_success(): void
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
    public function send_recovery_code_with_invalid_email(): void
    {
        $this->postJson('api/v1/password/recovery', [
            'email' => 'invalid-email',
        ])->assertStatus(422);
    }

    #[Test]
    public function reset_password_success()
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $user = User::query()->first();
        $code = $this->faker->randomNumber(6);

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
    public function reset_password_fail()
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $user = User::query()->first();
        $code = $this->faker->randomNumber(6);

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
    public function reset_password_validate_error(): void
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $user = User::query()->first();
        $code = $this->faker->randomNumber(6);

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
