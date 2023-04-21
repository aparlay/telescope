<?php

namespace Aparlay\Core\Api\V1\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;

class OptionalAuthenticate extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param mixed   ...$guards
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            // dont do anything
        }

        return $next($request);
    }
}
