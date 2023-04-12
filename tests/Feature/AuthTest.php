<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login with correct credentials test.
     */
    public function testUserCanLoginWithCorrectCredentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(201);
    }

    /**
     * Login with incorrect credentials test.
     */
    public function testUserCannotLoginWithIncorrectCredentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.auth.login'), [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Register with correct credentials test.
     */
    public function testUserCanRegisterWithCorrectCredentials(): void
    {
        $response = $this->postJson(route('api.auth.register'), [
            'name' => 'Piotr Zapolski',
            'email' => 'pz@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Piotr Zapolski',
            'email' => 'pz@example.com',
        ]);
    }

    /**
     * Register with incorrect credentials test.
     */
    public function testUserCannotRegisterWithIncorrectCredentials(): void
    {
        $response = $this->postJson(route('api.auth.register'), [
            'name' => 'Piotr Zapolski',
            'email' => 'pz@example.com',
            'password' => 'password',
            'password_confirmation' => 'wrong_password',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('users', [
            'name' => 'Piotr Zapolski',
            'email' => 'pz@example.com',
        ]);
    }
}
