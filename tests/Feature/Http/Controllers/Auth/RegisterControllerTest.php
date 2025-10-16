<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_via_email()
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
}
