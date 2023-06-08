<?php

namespace Tests\Unit\Repositories;

use App\Enums\Checks;
use App\Models\Check;
use App\Models\Contract;
use App\Models\Evaluation;
use App\Models\JobSite;
use App\Models\JobSiteShift;
use App\Models\User;
use App\Models\UserShift;
use Auth;
use Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Xguard\Tasklist\Models\Review;
use Xguard\Tasklist\Repositories\OnSiteEmployeeRepository;

class OnSiteEmployeeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    const GET_ON_SITE_EMPLOYEES = 'tasklist.get-on-site-employees';
    const MAX_JOB_SITE = 10;
    const ZERO = 0;
    const EIGHT_HOURS = 8;
    const FIVE = 5;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Auth::setUser($this->user);
    }

    private function setUpJobSiteShiftsAndReturnJobSite($passedJobSiteShifts = false)
    {
        $jobSite = factory(JobSite::class)->create();
        $contract = factory(Contract::class)->create([
            'job_site_id' => $jobSite->id,
        ]);
        for ($i = self::ZERO; $i < self::MAX_JOB_SITE; $i++) {
            if ($passedJobSiteShifts) {
                $jobSiteShift = factory(JobSiteShift::class)->create([
                    'contract_id' => $contract->id,
                    'shift_start' => (Carbon::now())->subWeek(),
                    'shift_end' => (Carbon::now())->subWeek(),
                ]);
            } else {
                $jobSiteShift = factory(JobSiteShift::class)->create([
                    'contract_id' => $contract->id,
                    'shift_start' => Carbon::now()->startOfDay(),
                    'shift_end' => Carbon::now()->endOfDay(),
                ]);
            }
            $newUser = factory(User::class)->create();

            Evaluation::create([
                'user_id' => $newUser->id,
                'work_quality' => self::FIVE,
                'punctuality' => self::FIVE,
                'team_work' => self::FIVE,
                'follows_directions' => self::FIVE,
                'communication' => self::FIVE,
                'average' => self::FIVE,
            ]);

            factory(UserShift::class)->create([
                'user_id' => $newUser->id,
                'job_site_shift_id' => $jobSiteShift->id,
            ]);

            factory(Check::class)->create([
                Check::USER_ID => $jobSiteShift->userShift->employee->id,
                Check::JOB_SITE_SHIFT_ID => $jobSiteShift->id,
                Check::CREATED_AT => Carbon::now()->endOfDay()->subHours(self::EIGHT_HOURS),
                Check::TYPE => Checks::CHECKOUT()->getValue()
            ]);
            factory(Check::class)->create([
                Check::USER_ID => $jobSiteShift->userShift->employee->id,
                Check::JOB_SITE_SHIFT_ID => $jobSiteShift->id,
                Check::CREATED_AT => Carbon::now()->startOfDay()->addHours(self::EIGHT_HOURS),
                Check::TYPE => Checks::CHECKIN()->getValue()
            ]);

            factory(Review::class)->create([
                Review::USER_SHIFT_ID => $jobSiteShift->userShift->id,
            ]);
        }
        return $jobSite;
    }

    public function testGetAllEmployeesFromJobSite()
    {
        $jobSite = $this->setUpJobSiteShiftsAndReturnJobSite();
        $employees = OnSiteEmployeeRepository::getOnSiteEmployees($jobSite->id, true);
        $this->assertCount(self::MAX_JOB_SITE, $employees);
    }

    public function testDontGetEmployeesFromJobSiteIfJobSiteShiftNotWithinSelectedDateTime()
    {
        $jobSite = $this->setUpJobSiteShiftsAndReturnJobSite(true);
        $employees = OnSiteEmployeeRepository::getOnSiteEmployees($jobSite->id, true);
        $this->assertCount(self::ZERO, $employees);
    }
}
