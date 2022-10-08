<?php

namespace App\Http\Middleware;

use Closure;

class RolesCheck
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
        if(!is_admin())
        {
            return redirect('/')->with('message','Not Authorized');
        }
        return $next($request);
    }
}