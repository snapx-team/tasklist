<?php

namespace Xguard\Tasklist\Actions\Employees;

use DB;
use Lorisleiva\Actions\Action;
use Throwable;
use Xguard\Tasklist\Models\Employee;

class CreateOrUpdateEmployeeAction extends Action
{
    public function rules(): array
    {
        return [
            "selectedUsers" => ['present', 'array'],
            "role" => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'selectedUsers.present' => 'No selected users',
            'role.required' => 'Employee role is required',
        ];
    }

    /**
     * @throws Throwable
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            foreach ($this->selectedUsers as $user) {
                $tasklist = Employee::updateOrCreate(
                    [Employee::USER_ID => $user['id']],
                    [Employee::ROLE => $this->role]
                );
            }
            DB::commit();
            return $tasklist;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
