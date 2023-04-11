<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
  public function handle($request, Closure $next, $role)
  {
    if (!Auth::guest()) {
      $roles = explode('|', $role);
      if (!in_array($request->user()->role,$roles)) {
//        abort(403, "No tienes autorización para ingresar.");
        return redirect('no-allowed');
      }
    } else {
      return redirect()->guest('login');
    }
    
    return $next($request);
  }
}
