<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CommentSent extends Notification implements ShouldQueue
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
        $message = "New {$notifiable->slack_admin_url} sent on {$notifiable->mediaObj->slack_admin_url}.";
        $message .= PHP_EOL . '_*Content:*_ ' . PHP_EOL . '> ' . $notifiable->text;

        return (new SlackMessage())
            ->to(config('app.slack_comment_sent'))
            ->content($message)
            ->success();
    }
}
