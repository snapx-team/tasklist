<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Xguard\Tasklist\Enums\SessionVariables;
use Xguard\Tasklist\Http\Controllers\AppController;
use Xguard\Tasklist\Models\Employee;

class AppControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        Auth::setUser($user);
        $tasklist = factory(Employee::class)->states('admin')->create(['user_id' => $user->id,]);
        session(['role' => 'admin', 'tasklist_id' => $tasklist->id]);
    }

    public function testSetTasklistSessionVariablesLoggedIn()
    {
        // Arrange
        $appController = new AppController();
        $user = new \stdClass();
        $user->id = 1;
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        // Act
        $result = $appController->setTasklistSessionVariables();

        // Assert
        $this->assertSame(['is_logged_in' => true], $result);

    }

    public function testSetTasklistSessionVariablesNotLoggedIn()
    {
        // Arrange
        $appController = new AppController();
        Auth::shouldReceive('check')->once()->andReturn(false);

        // Act
        $result = $appController->setTasklistSessionVariables();

        // Assert
        $this->assertSame(['is_logged_in' => false], $result);
    }

    public function testGetRoleAndEmployeeId()
    {
        // Arrange
        $appController = new AppController();
        session([SessionVariables::ROLE()->getValue() => 'admin']);
        session([SessionVariables::EMPLOYEE_ID()->getValue() => 1]);

        // Act
        $result = $appController->getRoleAndEmployeeId();

        // Assert
        $this->assertSame(['role' => 'admin', 'employee_id' => 1], $result);
    }

    public function testGetFooterInfo()
    {
        // Arrange
        $appController = new AppController();
        config(['tasklist.parent_name' => 'Parent Company']);
        config(['tasklist.version' => '1.2.3']);

        // Act
        $result = $appController->getFooterInfo();

        // Assert
        $this->assertSame([
            'parent_name' => 'Parent Company',
            'version' => '1.2.3',
            'date' => date("Y")
        ], $result);
    }
}

