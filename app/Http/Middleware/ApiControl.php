<?php

namespace App\Http\Middleware;

use Closure;

class ApiControl
{
    private $tokens = [
      1=>'', //'riad.virtual'
      2=>'', //'hotelrosadeoro.es' => 
      5=>'' //'siloeplaza.es' => 
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if (!isset($_SERVER['HTTP_TOKEN_API'])) 
        return response()->json ('no autorizado', 401);
      if (!in_array($_SERVER['HTTP_TOKEN_API'],$this->tokens)) 
        return response()->json ('no autorizado', 401);
      return $next($request);
    }
}
