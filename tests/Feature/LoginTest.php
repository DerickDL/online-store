<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * login successfully
     */
    public function test_login_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_login_failed(): void
    {
        $response = $this->post('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['error']);
    }
}
