<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Arr;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('signin');
        } else {
            return response('Unauthorized.', 401);
        }
    }*/


    protected $guards;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->guards = $guards;

        if ($guards == "root") {
            $this->guards = 'root';
            $this->authenticate($request, $this->guards);
            return $next($request);
        }
        
        if ($guards == "manager") {
            $this->guards = 'manager';
            $this->authenticate($request, $this->guards);
            return $next($request);
        } 

        if ($guards == "reseller") {
            $this->guards = 'reseller';
            $this->authenticate($request, $this->guards);
            return $next($request);
        } 

        if ($guards == "web") {
            $this->guards = 'web';
            $this->authenticate($request, $this->guards);
            return $next($request);
        } 

        $this->authenticate($request, $guards);
        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        $guards = Arr::get($this->guards, 0);
        switch($guards) {
            case 'root':
                if (! $request->expectsJson()) {
                    return route('signin');
                } else {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
                break;
            case 'manager':
                if (! $request->expectsJson()) {
                    return route('manager-login');
                } else {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
                break;
            
            case 'reseller':
                if (! $request->expectsJson()) {
                    return route('reseller-login');
                } else {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
                break;
            
            case 'web':
                if (! $request->expectsJson()) {
                    return route('signin');
                } else {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
                break;
            default: 
                if (! $request->expectsJson()) {
                    return route('signin');
                } else {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
        }

        if (! $request->expectsJson()) {
            return route('signin');
        } else {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
    }
}
