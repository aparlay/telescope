<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\QueueBusy;

class QueueBusyListener
{
    public function handle($event)
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify(new QueueBusy());
        }
    }
}
