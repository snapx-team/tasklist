<?php

namespace Xguard\Tasklist\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Xguard\Tasklist\Enums\Roles;
use Xguard\Tasklist\Models\Employee;

/**
 * Class IsAdmin
 *
 * Checks if an employee is an admin
 *
 * @package Xguard\Tasklist\Http\Middleware
 */
class IsAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $employee = Employee::where(Employee::USER_ID, '=', Auth::user()->id)->first();
            if ($employee->role === Roles::ADMIN()->getValue()) {
                return $next($request);
            }
        }
        return redirect('/');
    }
}
