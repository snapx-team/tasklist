<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Xguard\TaskList\Actions\AdminPageData\GetAdminPageDataAction;
use Xguard\Tasklist\Models\Employee;

class GetAdminPageDataActionTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        Auth::setUser($user);
        $tasklist = factory(Employee::class)->states('admin')->create(['user_id' => $user->id,]);
        session(['role' => 'admin', 'tasklist_id' => $tasklist->id]);

        factory(Employee::class, 2)->create();
    }

    public function testGetAdminPageDataIfAdmin()
    {
        $dashboardData = app(GetAdminPageDataAction::class)->run();
        $this->assertCount(3, $dashboardData['tasklists']);
    }

    public function testGetAdminPageDataActionThrowsErrorIfCoordinatorIsNotAdmin()
    {
        $this->expectException(AuthorizationException::class);
        session(['role' => 'tasklist', 'tasklist_id' => session('tasklist_id')]);
        app(GetAdminPageDataAction::class)->run();
    }
}
