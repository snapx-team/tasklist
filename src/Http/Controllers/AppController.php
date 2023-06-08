<?php

namespace Xguard\Tasklist\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Xguard\Tasklist\Actions\AdminPageData\GetAdminPageDataAction;
use Xguard\Tasklist\Actions\EmployeeProfileData\GetEmployeeProfileAction;
use Xguard\Tasklist\Enums\SessionVariables;
use Xguard\Tasklist\Models\Employee;

class AppController extends Controller
{
    public function getIndex()
    {
        $this->setTasklistSessionVariables();
        return view('Xguard\Tasklist::index');
    }

    public function setTasklistSessionVariables()
    {
        $strIsLoggedIn = 'is_logged_in';
        if (Auth::check()) {
            $employee = Employee::where(Employee::USER_ID, '=', Auth::user()->id)->first();
            session([SessionVariables::ROLE()->getValue() => $employee->role, SessionVariables::EMPLOYEE_ID()->getValue() => $employee->id]);
            return [$strIsLoggedIn => true];
        }
        return [$strIsLoggedIn => false];
    }

    public function getRoleAndEmployeeId(): array
    {
        return [
            SessionVariables::ROLE()->getValue() => session(SessionVariables::ROLE()->getValue()),
            SessionVariables::EMPLOYEE_ID()->getValue() => session(SessionVariables::EMPLOYEE_ID()->getValue()),
        ];
    }

    public function getFooterInfo(): array
    {
        return [
            'parent_name' => config('tasklist.parent_name'),
            'version' => config('tasklist.version'),
            'date' => date("Y")
        ];
    }

    public function getAdminPageData()
    {
            return app(GetAdminPageDataAction::class)->run();

    }

    public function getEmployeeProfile(): JsonResponse
    {
        try {
            $profile = (new GetEmployeeProfileAction())->run();
            return new JsonResponse($profile);
        } catch (\Exception $e) {
            return new JsonResponse([], $e->getCode(), $e->getMessage());
        }
    }
}
