<?php

namespace Aparlay\Core\Api\V1\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;

class CustomTrottle extends ThrottleRequests
{
    protected function resolveRequestSignature($request)
    {
        // Throttle by a particular header
        return $request->header('X-DEVICE-ID');
    }
}
