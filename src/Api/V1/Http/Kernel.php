<?php

namespace Aparlay\Core\Api\V1\Http;

use App\Http\Kernel as AluaKernel;

class Kernel extends AluaKernel
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
    ];
}
