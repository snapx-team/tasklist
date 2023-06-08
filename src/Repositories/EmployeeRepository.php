<?php

namespace Xguard\Tasklist\Repositories;

use Xguard\Tasklist\Models\Employee;

class EmployeeRepository
{
    public static function findOrFail(int $id): Employee
    {
        return Employee::findOrFail($id);
    }
}
