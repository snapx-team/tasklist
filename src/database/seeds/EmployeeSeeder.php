<?php

namespace Xguard\Tasklist\database\seeds;

use Illuminate\Database\Seeder;
use Xguard\Tasklist\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        Employee::create([
            'user_id' => 1,
            'role' => 'admin',
        ]);
        Employee::create([
            'user_id' => 2,
            'role' => 'admin',
        ]);
        Employee::create([
            'user_id' => 3,
            'role' => 'admin',
        ]);
    }
}
