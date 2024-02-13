<?php

namespace App\Http\Middleware;

use Closure;

class ActiveBranchCheck
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
        $user = auth('branch')->user();

        if (isset($user) && $user->status == 0){
            auth()->guard('branch')->logout();
            return redirect()->route('branch.auth.login');
        }
        return $next($request);
    }
}
