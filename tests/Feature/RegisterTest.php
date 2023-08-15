<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Successfully registered
     */
    public function test_registration_should_succeed(): void
    {
        $response = $this->post('/api/v1/auth/register', [
            'name'  => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'password' => 'Qwerty+123',
            'role' => 'user'
        ]);

        $response->assertStatus(201);
    }

    /**
     * Fail to register due to validation
     */
    public function test_registration_should_fail_because_of_invalid_email(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'  => 'John Doe',
            'email' => 'johndoe.com',
            'password' => 'Qwerty+123'
        ]);

        $response->assertJsonStructure([
            'errors' => [
                'email',
            ],
        ]);
        $response->assertStatus(422);
    }

    /**
     * Fail to register due to validation
     */
    public function test_registration_should_fail_because_of_invalid_password(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'  => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'password' => '1'
        ]);
        
        $response->assertJsonStructure([
            'errors' => [
                'password',
            ],
        ]);
        $response->assertStatus(422);
    }

        /**
     * Fail to register due to validation
     */
    public function test_registration_should_fail_because_of_missing_name(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'johndoe@gmail.com',
            'password' => 'Qwerty+123'
        ]);
        
        $response->assertJsonStructure([
            'errors' => [
                'name',
            ],
        ]);
        $response->assertStatus(422);
    }
}
