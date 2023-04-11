<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
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

        if (!Auth::guest()) {
            if ('admin' !== Auth::user()->role) {
                return redirect('403');//->guest('/admin/reservas');
            }
        }else{
            return redirect()->guest('login');
        }
        return $next($request);
    }
}
