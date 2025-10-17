<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function register_via_email_success()
    {
        $this->postJson('api/v1/register', [
            'email' => 'john@doe.com',
            'password' => 'seCret99@',
            'password_confirmation' => 'seCret99@',
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                ],
            ]);
    }

    #[Test]
    public function register_via_email_failed()
    {
        $response = $this->postJson('api/v1/register', [
            'email' => 'johndoe.com',
            'password' => 'se',
            'password_confirmation' => 'seCret99@',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'status',
            'message',
            'errors' => ['email', 'password'],
        ]);

        $json = $response->json();

        $this->assertArrayHasKey('message', $json);
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('email', $json['errors']);
        $this->assertArrayHasKey('password', $json['errors']);
        $this->assertSame(2, count($json['errors']));
        $this->assertSame(2, count($json['errors']['password']));
    }
}
