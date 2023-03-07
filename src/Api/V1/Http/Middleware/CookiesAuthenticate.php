<?php

namespace Aparlay\Core\Api\V1\Http\Middleware;

use Aparlay\Core\Models\Enums\UserStatus;
use Closure;
use Cookie;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
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
                $token = new Token($rawToken);
            } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
            }
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        // Log out suspended or blocked users
        try {
            if ($request->header('Authorization') !== null &&
                auth()->check() &&
                in_array(auth()->user()->status, [UserStatus::BLOCKED->value, UserStatus::SUSPENDED->value])) {
                auth()->logout(true);

                $cookie1 = Cookie::forget('__Secure_token');
                $cookie2 = Cookie::forget('__Secure_refresh_token');
                $cookie3 = Cookie::forget('__Secure_username');

                return response(json_encode(['message' => 'Account suspended or blocked']), Response::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/json'])
                    ->cookie($cookie1)
                    ->cookie($cookie2)
                    ->cookie($cookie3);
            }
        } catch (TokenBlacklistedException $e) {
        }

        return $next($request);
    }
}
