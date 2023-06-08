<?php

namespace Xguard\Tasklist\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xguard\Tasklist\Models\Employee;
use Xguard\Tasklist\Actions\Employees\CreateOrUpdateEmployeeAction;
use Xguard\Tasklist\Actions\Employees\DeleteEmployeeAction;

class EmployeeController extends Controller
{
    public function createEmployees(Request $request)
    {
        $employeeData = $request->all();

        try {
            app(CreateOrUpdateEmployeeAction::class)->fill([
                "selectedUsers" => $employeeData['selectedUsers'],
                "role" => $employeeData['role'],
            ])->run();
        } catch (\Exception $e) {
            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function deleteEmployee($id)
    {
        try {
            app(DeleteEmployeeAction::class)->fill([
                'tasklistId' => $id
            ])->run();
        } catch (\Exception $e) {
            return response([
                'success' => 'false',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getEmployees()
    {
        return Employee::with('user')->get();
    }
}
