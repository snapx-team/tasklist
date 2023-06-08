<?php

namespace Xguard\Tasklist\Actions\Employees;

use Exception;
use Lorisleiva\Actions\Action;
use Throwable;
use Xguard\Tasklist\Models\Employee;

class DeleteEmployeeAction extends Action
{

    public function rules(): array
    {
        return [
            'tasklistId' => ['required', 'integer', 'gt:0'],
        ];
    }

    /**
     * @throws Exception|Throwable
     */
    public function handle()
    {
        try {
            \DB::beginTransaction();
            $tasklist = Employee::findOrFail($this->tasklistId);
            $tasklist->delete();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
