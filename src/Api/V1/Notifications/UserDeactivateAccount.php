<?php

namespace Aparlay\Core\Api\V1\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class UserDeactivateAccount extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $message = 'This user was removed an account';
        $message .= PHP_EOL.'_*Email:*_ '.$notifiable->email;
        $message .= PHP_EOL.'_*Name:*_ '.$notifiable->full_name;

        return (new SlackMessage())
            ->to('#waptap-testing')
            ->content($message)
            ->success();
    }
}
