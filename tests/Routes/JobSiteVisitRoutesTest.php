<?php

namespace Tests\Routes;

use App\Models\JobSite;
use App\Models\JobSiteSubaddress;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Xguard\Tasklist\Models\JobSiteVisit;
use Xguard\Tasklist\Models\SupervisorShift;

/**
 * @group supervisor
 */
class JobSiteVisitRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const CREATE_JOB_SITE_SHIFT_VISIT = 'tasklist.create-job-site-visit';
    const UPDATE_JOB_SITE_SHIFT_VISIT = 'tasklist.update-job-site-visit';
    const DELETE_JOB_SITE_SHIFT_VISIT = 'tasklist.delete-job-site-visit';
    const JOB_SITE_ID = 'job_site_id';
    const ADDRESS = 'address';
    const FORMATTED_ADDRESS = 'formatted_address';
    const LONGITUDE = 'longitude';
    const LATITUDE = 'latitude';
    const ID = 'id';
    const INVALID_ID = 'invalid ID';

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Auth::setUser($this->user);
    }

    public function testCreateJobSiteVisitFromJobSite()
    {
        $jobSite = factory(JobSite::class)->create();
        $supervisorShift = factory(SupervisorShift::class)->create();
        $apiCall = route(self::CREATE_JOB_SITE_SHIFT_VISIT);
        $data = [
            JobSiteVisit::SUPERVISOR_SHIFT_ID => $supervisorShift->id,
            JobSiteVisit::ADDRESS_ID => $jobSite->id,
            JobSiteVisit::IS_PRIMARY_ADDRESS => true,
        ];
        $response = $this->post($apiCall, $data);
        $count = count(JobSiteVisit::all());
        $this->assertTrue($count == 1);
        $response->assertSuccessful()->assertJson([self::ID => 1]);
    }

    public function testCreateJobSiteVisitFromSubAddress()
    {
        $jobSite = factory(JobSite::class)->create();
        $jobSiteSubAddress = JobSiteSubaddress::create([
            self::JOB_SITE_ID => $jobSite->id,
            self::ADDRESS => $this->faker->address,
            self::FORMATTED_ADDRESS => $this->faker->address,
            self::LONGITUDE => $this->faker->longitude,
            self::LATITUDE => $this->faker->latitude,
        ]);
        $supervisorShift = factory(SupervisorShift::class)->create();
        $apiCall = route(self::CREATE_JOB_SITE_SHIFT_VISIT);
        $data = [
            JobSiteVisit::SUPERVISOR_SHIFT_ID => $supervisorShift->id,
            JobSiteVisit::ADDRESS_ID => $jobSiteSubAddress->id,
            JobSiteVisit::IS_PRIMARY_ADDRESS => false,
        ];
        $response = $this->post($apiCall, $data);
        $count = count(JobSiteVisit::all());
        $this->assertTrue($count == 1);
        $response->assertSuccessful()->assertJson([self::ID => 1]);
    }

    public function testUpdateJobSiteVisit()
    {
        $jobSiteVisit = factory(JobSiteVisit::class)->create();
        $apiCall = route(self::UPDATE_JOB_SITE_SHIFT_VISIT);
        $data = [
            JobSiteVisit::ID => $jobSiteVisit->id,
            JobSiteVisit::END_TIME => $this->faker->date(),
        ];
        $response = $this->patch($apiCall, $data);
        $response->assertOk();
    }

    public function testDeleteJobSiteVisit()
    {
        $jobSiteVisit = factory(JobSiteVisit::class)->create();
        $apiCall = route(self::DELETE_JOB_SITE_SHIFT_VISIT, ['visitId' => $jobSiteVisit->id]);
        $this->assertDatabaseHas('sa_job_site_visits', ['id' => $jobSiteVisit->id, 'deleted_at' => null]);
        $response = $this->delete($apiCall);
        $response->assertOk();
        $this->assertDatabaseMissing('sa_job_site_visits', ['id' => $jobSiteVisit->id, 'deleted_at' => null]);
    }

    public function testValidValidations()
    {
        $apiCall = route(self::CREATE_JOB_SITE_SHIFT_VISIT);
        $data = [
            JobSiteVisit::SUPERVISOR_SHIFT_ID => self::INVALID_ID,
            JobSiteVisit::ID => self::INVALID_ID,
        ];
        $response = $this->post($apiCall, $data);
        $response->assertJsonValidationErrors([JobSiteVisit::SUPERVISOR_SHIFT_ID, JobSiteVisit::IS_PRIMARY_ADDRESS, JobSiteVisit::ADDRESS_ID]);
    }
}
