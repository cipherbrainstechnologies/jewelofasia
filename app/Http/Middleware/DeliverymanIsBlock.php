<?php

namespace App\Http\Middleware;

use App\Model\DeliveryMan;
use Closure;

class DeliverymanIsBlock
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
        $user = DeliveryMan::where(['auth_token' => $request['token']])->first();

        if (isset($user) && $user->is_active == 0){
            $errors = [];
            $errors[] = ['code' => 'auth-001', 'message' => 'Unauthorized.'];
            return response()->json([
                'errors' => $errors
            ], 401);
        }
        return $next($request);
    }
}
