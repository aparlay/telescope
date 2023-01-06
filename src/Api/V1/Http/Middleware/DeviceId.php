<?php

namespace Aparlay\Core\Api\V1\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DeviceId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws ValidationException
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->header('X-DEVICE-ID'))) {
            throw ValidationException::withMessages(['device_id' => ['Device Id cannot be blank.']]);
        }

        return $next($request);
    }
}
