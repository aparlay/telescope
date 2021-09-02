<?php

namespace Aparlay\Core\Api\V1\Http\Middleware;

use Closure;
use Cookie;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Token;

class CookiesAuthenticate
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
        if ($request->header('Authorization') == null && Cookie::has('__Secure_token')) {
            try {
                $rawToken = Cookie::get('__Secure_token');
                $token = new Token($rawToken);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            }
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        return $next($request);
    }
}
