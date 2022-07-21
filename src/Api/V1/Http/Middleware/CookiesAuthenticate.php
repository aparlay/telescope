<?php

namespace Aparlay\Core\Api\V1\Http\Middleware;

use Closure;
use Cookie;
use PHPOpenSourceSaver\JWTAuth\Token;

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
                \Log::error('raw:' . $rawToken);
                $token = new Token($rawToken);
                \Log::error('val:' . $token);
            } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
            }
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        return $next($request);
    }
}
