<?php

namespace Tests\Routes;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Xguard\Tasklist\Models\LocationPing;
use Xguard\Tasklist\Models\SupervisorShift;

/**
 * @group supervisor
 */
class LocationPingRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const CREATE_LOCATION_PING = 'tasklist.create-location-ping';
    const ID = 'id';

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Auth::setUser($this->user);
    }

    public function testCreateLocationPing()
    {
        $supervisorShift = factory(SupervisorShift::class)->create();
        $apiCall = route(self::CREATE_LOCATION_PING);
        $data = [
            LocationPing::SUPERVISOR_SHIFT_ID => $supervisorShift->id,
            LocationPing::LAT => $this->faker->latitude,
            LocationPing::LNG => $this->faker->longitude,
        ];
        $response = $this->post($apiCall, $data);
        $count = count(LocationPing::all());
        $this->assertTrue($count == 1);
        $response->assertSuccessful()->assertJson([self::ID => 1]);
    }
}
