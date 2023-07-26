<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;
use Xguard\Tasklist\Models\Employee;
use Xguard\Tasklist\Repositories\EmployeeRepository;

class EmployeeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    const ID = 'id';
    const EMPLOYEE_TABLE_NAME = 'tl_employees';
    const USER_ID = 'user_id';
    const ROLE = 'role';
    const ADMIN = 'admin';
    const EMPLOYEE = 'employee';
    const DELETED_AT = 'deleted_at';

    /**
     * @throws Throwable
     */
    public function testCreateOrUpdateEmployee()
    {
        // Arrange
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $selectedUsers = [
            [self::ID => $user1->id],
            [self::ID => $user2->id],
        ];
        $role = self::ADMIN; // Sample role value

        // Act
        EmployeeRepository::createOrUpdateEmployee($selectedUsers, $role);

        // Assert
        $this->assertDatabaseCount(self::EMPLOYEE_TABLE_NAME, count($selectedUsers));
        $this->assertDatabaseHas(self::EMPLOYEE_TABLE_NAME, [self::USER_ID => $user1->id, self::ROLE => self::ADMIN]);

        //Arrange
        $role = self::EMPLOYEE;

        //Act
        EmployeeRepository::createOrUpdateEmployee($selectedUsers, $role);

        // Assert
        $this->assertDatabaseHas(self::EMPLOYEE_TABLE_NAME, [self::USER_ID => $user1->id, self::ROLE => self::EMPLOYEE]);
    }

    /**
     * @throws Throwable
     */
    public function testDeleteEmployee()
    {
        // Arrange
        $employee = factory(Employee::class)->create();
        $id = $employee->id;

        $this->assertDatabaseHas(self::EMPLOYEE_TABLE_NAME, [self::ID => $id, self::DELETED_AT => null]);

        // Act
        $response = EmployeeRepository::deleteEmployee($id);

        // Assert
        $this->assertDatabaseMissing(self::EMPLOYEE_TABLE_NAME, [self::ID => $id, self::DELETED_AT => null]);
    }

    public function testFindOrFail()
    {
        // Arrange
        $employee = factory(Employee::class)->create();
        $id = $employee->id;

        // Act
        $result = EmployeeRepository::findOrFail($id);

        // Assert
        $this->assertInstanceOf(Employee::class, $result);
        $this->assertEquals($id, $result->id);
    }
}
