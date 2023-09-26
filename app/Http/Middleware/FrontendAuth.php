<?php

namespace App\Http\Middleware;

use Closure;

class FrontendAuth
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
        if (!$request->session()->has('frontUser')) {
            return redirect()->route('user-login')
                ->with('errorM', 'Please login');
        }
        return $next($request);
    }
}
