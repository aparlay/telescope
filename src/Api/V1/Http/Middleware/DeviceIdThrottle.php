<?php

namespace Aparlay\Core\Api\V1\Http\Middleware;

use function Elliptic\random_int;

use Illuminate\Routing\Middleware\ThrottleRequests;

class DeviceIdThrottle extends ThrottleRequests
{
    protected function resolveRequestSignature($request)
    {
        if ($request->header('X-DEVICE-ID') == 'stress-test-4c55-acb3-952c2ae699f3') {
            return 'stress-test-4c55-acb3-'.random_int(1, 1000000);
        }
        // Throttle by a particular header
        return $request->header('X-DEVICE-ID');
    }
}
