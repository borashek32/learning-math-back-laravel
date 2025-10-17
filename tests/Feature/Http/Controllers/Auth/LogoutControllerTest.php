<?php

namespace Feature\Http\Controllers\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function logout()
    {
        $this->seed(UserSeeder::class);

        $user = User::query()->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('api/v1/logout')
            ->assertStatus(200);

        $this->assertEquals(null, $user->currentAccessToken());
    }

    #[Test]
    public function logout_from_all_devices()
    {
        $this->seed(UserSeeder::class);

        $user = User::query()->first();

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->createToken('auth_token')->plainTextToken;
        $this->assertEquals(2, $user->tokens()->count());

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('api/v1/logout/all')
            ->assertStatus(200);

        $this->assertEquals(null, $user->currentAccessToken());
        $this->assertEquals(0, $user->tokens()->count());
    }
}
