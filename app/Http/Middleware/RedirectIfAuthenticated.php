<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */

    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('root')->check()) {

            return redirect('/');

        } else if (Auth::guard('manager')->check()) {

            return redirect('manager');

        } else if (Auth::guard('reseller')->check()) {

            return redirect('resellers');

        } else if (Auth::guard('web')->check()) {

            return redirect('client');
            
        }

        return $next($request);
    }
}
