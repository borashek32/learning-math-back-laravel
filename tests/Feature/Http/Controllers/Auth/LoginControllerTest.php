<?php

namespace Tests\Feature\app\Http\Controllers\Auth;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);

        $this->user = User::query()->first();
    }

    public function test_user_login_via_email_success()
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

    public function test_user_login_via_email_failed()
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
