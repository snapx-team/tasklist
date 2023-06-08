<?php

namespace Tests\Unit\Actions\Coordinators;

use App\Enums\Roles;
use App\Models\Contract;
use App\Models\JobSite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Xguard\Tasklist\Actions\Contracts\GetContractsDataAction;
use Xguard\Tasklist\Models\Employee;
use Tests\SetsRolesAndPermissions;
use Xguard\Tasklist\Models\JobSiteVisit;
use Xguard\Tasklist\Models\SupervisorShift;

class GetSupervisorsDataActionTest extends TestCase
{
    use RefreshDatabase, SetsRolesAndPermissions;

    public function setUp(): void
    {
        parent::setUp();
        $this->setRolesAndPermissions();
        $this->user = factory(User::class)->create();
        $this->tasklist = factory(Employee::class)->states('admin')->create(['user_id' => $this->user->id]);
        Auth::setUser($this->user);
        session(['role' => 'admin', 'tasklist_id' => $this->user->id]);
    }

    public function testRetrieveOnlySupervisors()
    {
        $newUser = factory(User::class)->states('employee', 'verified')->create();
        $dateRage = ['start' => Carbon::now()->format('Y-m-d'), 'end' => Carbon::yesterday()->format('Y-m-d')];

        $supervisorsData = app(GetContractsDataAction::class)->fill(['dateRange' => $dateRage])->run();
        $this->assertFalse($supervisorsData['supervisorsData']->contains('id', $newUser->id));

        $newUser->assignRole(Roles::SUPERVISOR()->getValue());
        $supervisorsData = app(GetContractsDataAction::class)->fill(['dateRange' => $dateRage])->run();
        $this->assertTrue($supervisorsData['supervisorsData']->contains('id', $newUser->id));
    }


    public function testCanRetrieveAllVisitedJobSitesAndRelatedInfo()
    {
        $newUser = factory(User::class)->create();
        $newUser->assignRole(Roles::SUPERVISOR()->getValue());

        $jobSite = factory(JobSite::class)->create();
        factory(Contract::class)->create(['job_site_id' => $jobSite->id]);

        $supervisorShift = factory(SupervisorShift::class)->create(['user_id' => $newUser->id]);
        factory(JobSiteVisit::class)->create([
            'supervisor_shift_id' => $supervisorShift->id, 'job_site_id' => $jobSite->id
        ]);

        $uri = route('tasklist.get-supervisor-data', ['start' => Carbon::now()->format('Y-m-d'), 'end' => Carbon::yesterday()->format('Y-m-d')]);

        $this->getJson($uri)
            ->assertStatus(200)
            ->assertJsonStructure([
                'supervisorsData' => [
                    '*' => [
                        'id',
                        'fullName',
                        'email',
                        'supervisorShifts' => [
                            '*' => [
                                'id',
                                'startTime',
                                'endTime',
                                'isActive',
                                'jobSiteVisits' => [
                                    '*' => [
                                        'id',
                                        'startTime',
                                        'endTime',
                                        'jobSite' => [
                                            'id',
                                            'address',
                                            'lat',
                                            'lng',
                                            'contracts' => [
                                                '*' => [
                                                    'id',
                                                    'name'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
