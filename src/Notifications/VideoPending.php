<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class VideoPending extends Notification implements ShouldQueue
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
        $message = "New post from is waiting for moderation {$notifiable->slack_admin_url}.";
        $message .= PHP_EOL.'_*Log:*_ '.PHP_EOL.implode("\n", $notifiable->processing_log);
        $message .= PHP_EOL.'_*Errors:*_ '.PHP_EOL.implode("\n", $notifiable->firstErrors);

        return (new SlackMessage())
            ->to('waptap-testing')
            ->content($message)
            ->success();
    }
}
