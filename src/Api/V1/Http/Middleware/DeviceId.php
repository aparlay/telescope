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
     * @throws ValidationException
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->header('X-DEVICE-ID'))) {
            throw ValidationException::withMessages(['device_id' => ['Device Id cannot be blank.']]);
        }

        return $next($request);
    }
}
