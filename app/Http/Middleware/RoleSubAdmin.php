<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleSubAdmin
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
            if (!preg_match('/subadmin/i', Auth::user()->role) 
                    && !preg_match('/admin/i', Auth::user()->role) 
                    && !preg_match('/agente/i', Auth::user()->role) 
                    && !preg_match('/recepcionista/i', Auth::user()->role) 
                    && !preg_match('/limpieza/i', Auth::user()->role)) {
                $room = \App\Rooms::where('owned', Auth::user()->id)->first();
                if ($room){
                  return redirect()->guest('/admin/propietario/'.$room->nameRoom);
                }
                return redirect()->guest('/admin/propietario/');
            }else if(preg_match('/limpieza/i', Auth::user()->role)){
                return redirect()->guest('/admin/limpieza');
            }
        }else{
            return redirect()->guest('login');
        }
        return $next($request);
    }
}
