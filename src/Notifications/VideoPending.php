<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class VideoPending extends Notification
{
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
        $message = "New {$notifiable->slack_admin_url} is waiting for moderation.";
        $message .= PHP_EOL.'_*Log:*_ '.PHP_EOL.implode("\n", $notifiable->processing_log);

        return (new SlackMessage())
            ->to('#waptap-testing')
            ->content($message)
            ->success();
    }
}
