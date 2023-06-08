<?php

namespace Tests\Routes;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Xguard\Tasklist\Models\Odometer;
use Xguard\Tasklist\Models\SupervisorShift;

/**
 * @group supervisor
 */
class SupervisorShiftRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const COORDINATOR_CREATE_SHIFT = 'tasklist.create-shift';
    const COORDINATOR_UPDATE_SHIFT = 'tasklist.update-shift';
    const COORDINATOR_CHECK_PREVIOUS_SHIFT = 'tasklist.check-previous-shift';
    const USER_ID = 'userId';
    const ODOMETER_ID = 'odometer_id';

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Auth::setUser($this->user);
    }

    public function testCreateSupervisorShift()
    {
        $user = factory(User::class)->states(['verified', 'employee'])->create();
        $apiCall = route(self::COORDINATOR_CREATE_SHIFT);
        $data = [
            SupervisorShift::USER_ID => $user->id,
            SupervisorShift::START_LAT => $this->faker->latitude,
            SupervisorShift::START_LNG => $this->faker->longitude,
        ];
        $response = $this->post($apiCall, $data);
        $count = count(SupervisorShift::all());
        $this->assertTrue($count == 1);
        $response->assertSuccessful()->assertJson([SupervisorShift::ID => 1]);
    }

    public function testUpdateSupervisorShift()
    {
        $supervisorShift = factory(SupervisorShift::class)->create();
        $apiCall = route(self::COORDINATOR_UPDATE_SHIFT);
        $data = [
            SupervisorShift::ID => $supervisorShift->id,
            SupervisorShift::END_LAT => $this->faker->latitude,
            SupervisorShift::END_LNG => $this->faker->longitude,
        ];
        $response = $this->patch($apiCall, $data);
        $response->assertOk();
    }

    public function testUserIdMustBeValidValidation()
    {
        $apiCall = route(self::COORDINATOR_CREATE_SHIFT);
        $data = [
            SupervisorShift::USER_ID => 'invalid user id',
        ];
        $response = $this->post($apiCall, $data);
        $response->assertJsonValidationErrors([SupervisorShift::USER_ID]);
    }

    public function testCheckPreviousShift()
    {
        $apiCall = route(self::COORDINATOR_CHECK_PREVIOUS_SHIFT, [self::USER_ID => 1]);
        $response1 = $this->get($apiCall);
        $response1->assertSuccessful()->assertJson([SupervisorShift::ID => null]);

        $supervisorShift = factory(SupervisorShift::class)->create([SupervisorShift::USER_ID => 1]);
        $odometer = factory(Odometer::class)->create([Odometer::SUPERVISOR_SHIFT_ID => $supervisorShift->id]);
        $response2 = $this->get($apiCall);
        $response2->assertSuccessful()->assertJson([SupervisorShift::ID => $supervisorShift->id, self::ODOMETER_ID => $odometer->id ]);
    }
}
