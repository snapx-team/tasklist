<?php

namespace Xguard\Tasklist\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;
use Xguard\Tasklist\Models\Employee;

/**
 * Class EmployeeRepository
 * @package Xguard\Tasklist\Repositories
 */
class EmployeeRepository
{
    public static function findOrFail(int $id): Employee
    {
        return Employee::findOrFail($id);
    }

    /**
     * Create or update employee
     *
     * @param array $selectedUsers
     * @param string $role
     * @return JsonResponse
     * @throws Throwable
     * @static
     */
    public static function createOrUpdateEmployee(array $selectedUsers, string $role): JsonResponse
    {
        try {
            DB::beginTransaction();
            foreach ($selectedUsers as $user) {
                $tasklist = Employee::updateOrCreate(
                    [Employee::USER_ID => $user['id']],
                    [Employee::ROLE => $role]
                );
            }
            DB::commit();
            return response()->json(['success' => true,]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete Employee
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public static function deleteEmployee(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $tasklist = Employee::findOrFail($id);
            $tasklist->delete();
            DB::commit();
            return response()->json(['success' => true,]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
