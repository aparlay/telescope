<?php

namespace Aparlay\Core\Api\V1\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The package route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'device-id' => \Aparlay\Core\Api\V1\Http\Middleware\DeviceId::class,
        'cookies-auth' => \Aparlay\Core\Api\V1\Http\Middleware\CookiesAuthenticate::class,
        'custom-throttle' => \Aparlay\Core\Api\V1\Http\Middleware\CustomTrottle::class,
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
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ];
}
