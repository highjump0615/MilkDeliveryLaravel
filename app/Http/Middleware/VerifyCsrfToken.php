<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

//    public function handle($request, Closure $next)
//    {
//        return parent::handle($request, $next);
//    }
//    /**
//      Determine if the session and input CSRF tokens match.
//      @param \Illuminate\Http\Request $request
//      @return bool 18
//    */
//    protected function tokensMatch($request) {
//    	$token = $request->ajax() ? $request->header('X-CSRF-Token') : $request->input('_token');
//    	return $request->session()->token() == $token;
//    }
}
