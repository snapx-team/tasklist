<?php

namespace Tests\Unit\Actions\Users;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Xguard\Tasklist\Actions\EmployeeProfileData\GetEmployeeProfileAction;
use Xguard\Tasklist\Models\Employee;

class GetCoordinatorProfileActionTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Auth::setUser($this->user);
        $this->tasklist = factory(Employee::class)->states('admin')->create(['user_id' => $this->user->id]);
        session(['role' => 'admin', 'tasklist_id' => $this->tasklist->id]);
    }

    public function testGetUserProfileAction()
    {
        $result = (new GetEmployeeProfileAction)->run();
        $this->assertEquals($this->tasklist->user->full_name, $result['userName']);
        $this->assertEquals($this->tasklist->role, $result['userStatus']);
        $this->assertEquals(Carbon::parse($this->tasklist->created_at)->toDateString(), $result['userCreatedAt']);
        $this->assertEquals($this->tasklist->user->locale, $result['language']);
    }
}
