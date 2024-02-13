<?php

namespace App\Http\Middleware;

use Closure;
use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;

class ModulePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module)
    {
        if (Helpers::module_permission_check($module)) {
            return $next($request);
        }
        Toastr::error('Access Denied !');
        return back();
    }
}
