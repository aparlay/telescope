<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Api\V1\Services\UserService;
use Illuminate\Auth\Events\Authenticated;

class LogAuthenticated
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
     * @param  Authenticated  $event
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(Authenticated $event)
    {
        $userService = app()->make(UserService::class);
        $userService->logUserDevice(
            auth()->user(),
            request()->userAgent(),
            request()->header('X-DEVICE-ID'),
            request()->ip()
        );
    }
}
