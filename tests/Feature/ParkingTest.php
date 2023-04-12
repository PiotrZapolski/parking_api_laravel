<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User can start parking.
     */
    public function testUserCanStartParking()
    {
        $this->seed();

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $response = $this->actingAs($user)->postJson(route('api.parking.start'), [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'start_time'  => now()->toDateTimeString(),
                    'stop_time'   => null,
                    'total_price' => 0,
                ],
            ]);

        $this->assertDatabaseCount('parkings', '1');
    }

    /**
     * User can sget ongoing parking with correct price.
     */
    public function testUserCanGetOngoingParkingWithCorrectPrice()
    {
        $this->seed();

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $this->actingAs($user)->postJson(route('api.parking.start'), [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);

        //3rd hour started already
        $this->travel(121)->minutes();

        $parking = Parking::first();
        $response = $this->actingAs($user)->getJson(route('api.parking.show', $parking->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'stop_time'   => null,
                    'total_price' => $zone->price_per_hour * 3,
                ],
            ]);
    }

    /**
     * User can stop parking test.
     */
    public function testUserCanStopParking()
    {
        $this->seed();

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();

        $this->actingAs($user)->postJson(route('api.parking.start'), [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);

        $this->travel(2)->hours();

        $parking = Parking::first();
        $response = $this->actingAs($user)->putJson(route('api.parking.stop', $parking->id));

        $updatedParking = Parking::find($parking->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'start_time'  => $updatedParking->start_time->toDateTimeString(),
                    'stop_time'   => $updatedParking->stop_time->toDateTimeString(),
                    'total_price' => $updatedParking->total_price,
                ],
            ]);

        $this->assertDatabaseCount('parkings', '1');
    }
}
