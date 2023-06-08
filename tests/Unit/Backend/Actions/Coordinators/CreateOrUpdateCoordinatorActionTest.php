<?php

namespace Tests\Unit\Actions\Coordinators;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Xguard\Tasklist\Actions\Employees\CreateOrUpdateEmployeeAction;
use Xguard\Tasklist\Models\Employee;

class CreateOrUpdateCoordinatorActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $tasklist = factory(Employee::class)->states('admin')->create(['user_id' => $user->id,]);
        Auth::setUser($user);
        session(['role' => 'admin', 'tasklist_id' => $tasklist->id]);
    }

    public function testCreateOrUpdateCoordinatorActionTest()
    {
        $users = factory(User::class, 2)->create();
        factory(Employee::class)->states('admin')->create(['user_id' => $users[0]->id]);
        app(CreateOrUpdateEmployeeAction::class)->fill([
            'selectedUsers' => [
                [
                    'id' => $users[0]->id,
                ],
                [
                    'id' => $users[1]->id,
                ]
            ],
            'role' => 'admin'
        ])->run();

        $this->assertDatabaseHas('sa_tasklists', ['user_id' => $users[0]->id]);
        $this->assertDatabaseHas('sa_tasklists', ['user_id' => $users[1]->id]);
    }
}
