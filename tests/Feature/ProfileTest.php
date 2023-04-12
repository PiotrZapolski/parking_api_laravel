<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Authorized user can get their profile test.
     */
    public function testAuthorizedUserCanGetTheirProfile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('api.profile.show'));

        $response->assertStatus(200)
            ->assertJsonStructure(['user' => ['name', 'email']])
            ->assertJsonCount(2, 'user')
            ->assertJsonFragment(['name' => $user->name])
            ->assertJsonFragment(['email' => $user->email]);
    }

    /**
     * User can update their profile with correct data test.
     */
    public function testUserCanUpdateTheirProfileWithCorrectData(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route('api.profile.update'), [
            'name' => 'Piotr',
            'email' => 'piotr@email.com',
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['user' => ['name', 'email']])
            ->assertJsonCount(2, 'user')
            ->assertJsonFragment(['name' => 'Piotr'])
            ->assertJsonFragment(['email' => 'piotr@email.com']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Piotr',
            'email' => 'piotr@email.com',
        ]);
    }

    /**
     * User can update their profile with correct data test.
     */
    public function testUserCanChangeTheirPasswordWithCorrectData(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route('api.profile.password.update'), [
            'current_password' => 'password',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertStatus(202);
    }

    /**
     * Unauthorized user cannot get their profile test.
     */
    public function testUnauthorizedUserCannotGetProfile(): void
    {
        $response = $this->getJson(route('api.profile.show'));

        $response->assertStatus(401);
    }

    /**
     * User can't update their profile with incorrect data test.
     */
    public function testUserCannotUpdateTheirProfileWithIncorrectData(): void
    {
        $users = User::factory()->count(2)->create();

        $response = $this->actingAs($users[0])->putJson(route('api.profile.update'), [
            'name' => 'Piotr',
            'email' => $users[1]->email,
        ]);

        $response->assertStatus(422);
    }

    /**
     * User can't update their password with incorrect data.
     */
    public function testUserCannotChangeTheirPasswordWithIncorrectData(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route('api.profile.password.update'), [
            'current_password' => 'wrong_password',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertStatus(422);
    }
}
