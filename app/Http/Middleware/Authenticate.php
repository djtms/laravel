<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Contracts\Auth\Guard;


class Authenticate
{   
    public function __construct(Guard $auth)
    {
        
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {     
    	if(!Auth::check()){
        	return redirect('/');
        }
    	
    	return $next($request);
    }
}
