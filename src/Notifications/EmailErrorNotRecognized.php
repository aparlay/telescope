<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Api\V1\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class EmailErrorNotRecognized extends Notification
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
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  Email  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $message = "Email {$notifiable->to} does not receive our {$notifiable->type} email but we couldn't recognize the error:";
        $message .= PHP_EOL.'_*Error:*_ '.PHP_EOL.'> '.$notifiable->error;

        return (new SlackMessage())
            ->to(config('app.slack_error'))
            ->content($message)
            ->success();
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
