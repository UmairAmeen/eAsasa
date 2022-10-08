<?php

namespace App\Http\Middleware;
use App\Http\Controllers\LicenseController;
use Closure;

class LicenseVerification
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
        $license = new LicenseController();
        if($license->isValidLicense())
        {
            return $next($request);
        }
        return redirect('/invalidLicense');
    }
}
