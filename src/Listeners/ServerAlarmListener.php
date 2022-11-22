<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Events\ServerAlarmEvent;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\ServerAlarm;

class ServerAlarmListener
{
    public function handle(ServerAlarmEvent $event)
    {
        if (($user = User::admin()->first()) !== null) {
            $message = ':fire: _*Attention:*_ Server Resource Limitation';
            foreach ($event->messages as $title => $message) {
                $message .= PHP_EOL.'_*'.$title.':*_ '.$message;
            }

            $user->notify(new ServerAlarm($message));
        }
    }
}
