<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        $this->user = User::query()->first();
    }

    #[Test]
    public function userLoginViaEmailSuccess(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $this->assertEquals(200, $response->status());

        $json = $response->json();

        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('access_token', $json['data']);
        $this->assertArrayHasKey('token_type', $json['data']);
    }

    #[Test]
    public function userLoginViaEmailFailed(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => $this->user->email,
            'password' => 'password1324',
        ]);

        $this->assertEquals(401, $response->status());

        $json = $response->json();

        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('message', $json['data']);
    }
}
