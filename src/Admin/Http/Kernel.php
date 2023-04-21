<?php

namespace Aparlay\Core\Admin\Http;

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
    protected $routeMiddleware    = [
        'admin-auth' => \Aparlay\Core\Admin\Http\Middleware\Authenticate::class,
        'role' => \Maklad\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Maklad\Permission\Middlewares\PermissionMiddleware::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * Forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
    protected $middlewarePriority = [
    ];
}
