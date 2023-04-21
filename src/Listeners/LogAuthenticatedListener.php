<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Api\V1\Services\UserService;
use Aparlay\Core\Helpers\IP;

class LogAuthenticatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function handle($event)
    {
        if (auth()->check()) {
            $userService = app()->make(UserService::class);
            $userService->logUserDevice(
                auth()->user(),
                request()->userAgent(),
                request()->header('X-DEVICE-ID'),
                IP::trueAddress()
            );
        }
    }
}
