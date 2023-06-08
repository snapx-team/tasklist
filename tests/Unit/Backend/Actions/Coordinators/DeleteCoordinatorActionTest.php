<?php

namespace Tests\Unit\Actions\Coordinators;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Xguard\Tasklist\Actions\Employees\DeleteEmployeeAction;
use Xguard\Tasklist\Models\Employee;

class DeleteCoordinatorActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->tasklist = factory(Employee::class)->states('admin')->create(['user_id' => $this->user->id]);
        Auth::setUser($this->user);
        session(['role' => 'admin', 'tasklist_id' => $this->user->id]);
    }

    public function testDeletionOfCoordinator()
    {
        $newUser = factory(User::class)->create();
        $newCoordinator = factory(Employee::class)->states('admin')->create(['user_id' => $newUser->id]);

        app(DeleteEmployeeAction::class)->fill(['tasklistId' => $newCoordinator->id])->run();

        $this->assertNull(Employee::where('id', $newCoordinator->id)->first());
    }
}
