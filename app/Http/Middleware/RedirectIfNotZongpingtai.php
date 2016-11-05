<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotZongpingtai
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'zongpingtai')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect('/zongpingtai');
        }

        return $next($request);
    }
}
