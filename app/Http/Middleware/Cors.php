<?php
 namespace App\Http\Middleware;

  use Closure;

  class Cors
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

         if ($request->getMethod() == "OPTIONS") {
             return response(['OK'], 200)
             ->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET,POST',
            'Access-Control-Allow-Headers' => 'Authorization,Content-Type,X-Requested-With,XMLHttpRequest',
          ]);
    }

    return $next($request)
    ->header('Access-Control-Allow-Origin', 'https://miramarski.com')
    ->header('Access-Control-Allow-Methods', 'POST')
    ->header('Access-Control-Allow-Headers','Authorization,Content-Type,X-Requested-With,XMLHttpRequest');

      }
  }