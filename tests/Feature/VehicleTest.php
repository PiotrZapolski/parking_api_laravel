<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User can get only their vehicles test.
     */
    public function testUserCanGetOnlyTheirOwnVehicles(): void
    {
        $users = User::factory()->count(2)->create();

        $user1 = $users[0];
        $user2 = $users[1];

        $users1Vehicle = Vehicle::factory()->create([
            'user_id' => $user1->id,
        ]);

        $users2Vehicle = Vehicle::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->getJson(route('api.vehicles.index'));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'plate_number']]])
            ->assertJsonPath('data.0.plate_number', $users1Vehicle->plate_number)
            ->assertJsonMissing($users2Vehicle->toArray());
    }

    /**
     * User can create vehicle test.
     */
    public function testUserCanCreateVehicle()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/vehicles', [
            'plate_number' => 'AAA111',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => ['0' => 'plate_number'],
            ])
            ->assertJsonPath('data.plate_number', 'AAA111');

        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA111',
        ]);
    }

    /**
     * User update their vehicle test.
     */
    public function testUserCanUpdateTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson('/api/v1/vehicles/' . $vehicle->id, [
            'plate_number' => 'AAA123',
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['plate_number'])
            ->assertJsonPath('plate_number', 'AAA123');

        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA123',
        ]);
    }

    /**
     * User can delete vehicle test.
     */
    public function testUserCanDeleteTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson('/api/v1/vehicles/' . $vehicle->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicle->id,
            'deleted_at' => NULL
        ])->assertDatabaseCount('vehicles', 0);
    }
}
