<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class CustomerIsBlocked
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
        $user = auth()->user();

        if (isset($user) && $user->is_block == 1){
            $errors = [];
            $errors[] = ['code' => 'auth-001', 'message' => 'Unauthorized.'];
            return response()->json([
                'errors' => $errors
            ], 401);
        }
        return $next($request);
    }
}
