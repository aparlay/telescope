<?php

namespace Aparlay\Core\Api\V1\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The package route middleware.
     *
     * These middlewares may be assigned to group or used individually.
     *
     * @var array
     */
    protected $routeMiddleware    = [
        'device-id' => \Aparlay\Core\Api\V1\Http\Middleware\DeviceId::class,
        'cookies-auth' => \Aparlay\Core\Api\V1\Http\Middleware\CookiesAuthenticate::class,
        'optional-auth' => \Aparlay\Core\Api\V1\Http\Middleware\OptionalAuthenticate::class,
        'device-id-throttle' => \Aparlay\Core\Api\V1\Http\Middleware\DeviceIdThrottle::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * Forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
    protected $middlewarePriority = [
        \Aparlay\Core\Api\V1\Http\Middleware\CookiesAuthenticate::class,
        \Aparlay\Core\Api\V1\Http\Middleware\DeviceId::class,
        \Aparlay\Core\Api\V1\Http\Middleware\OptionalAuthenticate::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Aparlay\Core\Api\V1\Http\Middleware\DispatchAuthenticatedEndpointEvent::class,
    ];
}
