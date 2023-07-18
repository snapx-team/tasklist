<?php

namespace Xguard\Tasklist\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use Xguard\Tasklist\Enums\SessionVariables;
use Xguard\Tasklist\Models\Employee;
use Xguard\Tasklist\Repositories\EmployeeRepository;

class EmployeeController extends Controller
{
    const USER_NAME = 'userName';
    const USER_STATUS = 'userStatus';
    const USER_CREATED_AT = 'userCreatedAt';
    const LANGUAGE = 'language';

    /**
     * Create Employee
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function createEmployees(Request $request): JsonResponse
    {
        $employeeData = $request->all();
        return EmployeeRepository::createOrUpdateEmployee($employeeData['selectedUsers'], $employeeData['role']);
    }

    /**
     * Delete Employee
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function deleteEmployee(int $id): JsonResponse
    {
        return EmployeeRepository::deleteEmployee($id);
    }

    /**
     * Get employees
     *
     * @return Builder[]|Collection
     */

    public function getEmployees()
    {
        return Employee::with('user')->get();
    }

    /**
     * get Employee Profile info
     *
     * @return array
     */
    public function getEmployeeProfile(): array
    {
        $employee = Employee::with(Employee::USER_RELATION_NAME)->get()->find(session(SessionVariables::EMPLOYEE_ID()->getValue()));

        return [
            self::USER_NAME => $employee->user->full_name,
            self::USER_STATUS => $employee->role,
            self::USER_CREATED_AT => Carbon::parse($employee->created_at)->toDateString(),
            self::LANGUAGE => $employee->user->locale
        ];
    }
}
