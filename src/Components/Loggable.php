<?php

namespace Aparlay\Core\Components;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\JobFailed;
use Illuminate\Notifications\Notification;
use Throwable;

trait Loggable
{
    public function log(Notification $notification): void
    {
        if (($user = User::admin()->first()) !== null) {
            $user->notify($notification);
        }
    }
}
