<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Api\V1\Services\OnlineUserService;

class OnlineUsersListener
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
     * @param object $event
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function handle($event)
    {
        if (auth()->check()) {
            $onlineUserService = app()->make(OnlineUserService::class);
            $onlineUserService->online(auth()->user());
        }
    }
}
