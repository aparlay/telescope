<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Api\V1\Services\UserService;
use Aparlay\Core\Models\User;
use Illuminate\Support\Facades\Redis;

class OnlineUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        UserService::online(auth()->user());
    }
}
