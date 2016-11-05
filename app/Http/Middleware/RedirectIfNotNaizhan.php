<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotNaizhan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'naizhan')
    {
        if (!Auth::guard($guard)->check()) {
            return redirect('/naizhan');
        }

        return $next($request);
    }
}
