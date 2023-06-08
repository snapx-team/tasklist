<?php

namespace Xguard\Tasklist\Actions\EmployeeProfileData;

use Xguard\Tasklist\Enums\SessionVariables;
use Xguard\Tasklist\Models\Employee;
use Carbon;
use Lorisleiva\Actions\Action;

class GetEmployeeProfileAction extends Action
{

    const USER_NAME = 'userName';
    const USER_STATUS = 'userStatus';
    const USER_CREATED_AT = 'userCreatedAt';
    const LANGUAGE = 'language';

    public function handle(): array
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
