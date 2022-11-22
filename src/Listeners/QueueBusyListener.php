<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\QueueBusy;

class QueueBusyListener
{
    public function handle($event)
    {
        if (($user = User::admin()->first()) !== null) {

            $message = '_*Attention:*_ Queue ' . $event->queue;
            $message .= ' is too busy please check horizon or make sure you have enough workers.';
            $message .= PHP_EOL.'_*Connections:*_ '.$event->connection;
            $message .= PHP_EOL.'_*Queue:*_ '.$event->queue;
            $message .= PHP_EOL.'_*Size:*_ '.$event->size;

            $user->notify(new QueueBusy($message));
        }
    }
}
