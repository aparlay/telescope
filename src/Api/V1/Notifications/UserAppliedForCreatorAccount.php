<?php

namespace Aparlay\Core\Api\V1\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class UserAppliedForCreatorAccount extends Notification implements ShouldQueue
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
        $message = $notifiable->slack_admin_url;
        $message .= ' is applied for content creator account';
        $message .= PHP_EOL . '_*Country:*_ ' . $notifiable->country_label;
        $message .= PHP_EOL . '_*Gender:*_ ' . $notifiable->gender_label;

        return (new SlackMessage())
            ->to(config('app.slack_apply_for_verification'))
            ->content($message)
            ->success();
    }
}
