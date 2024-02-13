<?php

namespace App\Http\Middleware;

use Closure;

class EmployeeActiveCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth('admin')->user();
        if (isset($user) && $user->status == 0) {
            auth()->guard('admin')->logout();
            return redirect()->route('admin.auth.login');

        }
        return $next($request);
    }
}
