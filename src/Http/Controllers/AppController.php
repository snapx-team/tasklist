<?php

namespace Xguard\Tasklist\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Xguard\Tasklist\Enums\SessionVariables;
use Xguard\Tasklist\Models\Employee;

/**
 * Class AppController
 * @package Xguard\Tasklist\Http\Controllers
 */
class AppController extends Controller
{
    /**
     * Get TaskList Index View
     *
     * @return Application|Factory|View
     */
    public function getIndex()
    {
        $this->setTasklistSessionVariables();
        return view('Xguard\Tasklist::index');
    }

    /**
     * Set Task List Sessions Variables and returns logged-in status
     *
     * @return array
     */
    public function setTasklistSessionVariables(): array
    {
        $strIsLoggedIn = 'is_logged_in';
        if (Auth::check()) {
            $employee = Employee::where(Employee::USER_ID, '=', Auth::user()->id)->first();
            session([SessionVariables::ROLE()->getValue() => $employee->role, SessionVariables::EMPLOYEE_ID()->getValue() => $employee->id]);
            return [$strIsLoggedIn => true];
        }
        return [$strIsLoggedIn => false];
    }

    /**
     * Retrieve role and employee ID
     *
     * @return array
     */
    public function getRoleAndEmployeeId(): array
    {
        return [
            SessionVariables::ROLE()->getValue() => session(SessionVariables::ROLE()->getValue()),
            SessionVariables::EMPLOYEE_ID()->getValue() => session(SessionVariables::EMPLOYEE_ID()->getValue()),
        ];
    }

    /**
     * Gets to footer info from config file
     *
     * @return array
     */
    public function getFooterInfo(): array
    {
        return [
            'parent_name' => config('tasklist.parent_name'),
            'version' => config('tasklist.version'),
            'date' => date("Y")
        ];
    }
}
