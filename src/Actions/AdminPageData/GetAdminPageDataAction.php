<?php

namespace Xguard\Tasklist\Actions\AdminPageData;

use Lorisleiva\Actions\Action;
use Xguard\Tasklist\Models\Employee;
use Xguard\Tasklist\Enums\Roles;
use Xguard\Tasklist\Enums\SessionVariables;

class GetAdminPageDataAction extends Action
{
    const EMPLOYEES = 'employees';

    public function authorize(): bool
    {
        return (session(SessionVariables::ROLE()->getValue()) ===  Roles::ADMIN()->getValue());
    }

    public function handle(): array
    {
        $employees = Employee::with(Employee::USER_RELATION_NAME)->get();

        return [
            self::EMPLOYEES => $employees,
        ];
    }
}
