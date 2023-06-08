<?php

namespace Xguard\Tasklist\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Xguard\Tasklist\Models\Employee;

class CheckHasAccess
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $tasklist = Employee::where(Employee::USER_ID, '=', Auth::user()->id)->first();
            if ($tasklist === null) {
                abort(403, "You need to be added to the tasklist app. Please ask an admin for access.");
            }
        } else {
            abort(403, "Please first login to ERP");
        }
        return $next($request);
    }
}
