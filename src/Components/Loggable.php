<?php

namespace Aparlay\Core\Components;

use Aparlay\Core\Models\User;
use Illuminate\Notifications\Notification;

trait Loggable
{
    public function log(Notification $notification): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify($notification);
        }
    }
}
