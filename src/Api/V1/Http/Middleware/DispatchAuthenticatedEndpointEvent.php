<?php

namespace Aparlay\Core\Api\V1\Http\Middleware;

use Aparlay\Core\Events\DispatchAuthenticatedEndpoints;
use Closure;

class DispatchAuthenticatedEndpointEvent
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
        DispatchAuthenticatedEndpoints::dispatch();

        return $next($request);
    }
}