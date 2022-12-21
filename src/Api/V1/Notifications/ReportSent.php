<?php

namespace Aparlay\Core\Api\V1\Notifications;

use Aparlay\Core\Api\V1\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ReportSent extends Notification implements ShouldQueue
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
     * @param Report $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $message = $notifiable->creatorObj->slack_admin_url ?? 'A Guest user';
        $message .= ' reported '.$notifiable->slack_subject_admin_url;
        $message .= PHP_EOL.'_*Reason:*_ '.$notifiable->reason;

        return (new SlackMessage())
            ->to(config('app.slack_report'))
            ->content($message)
            ->success();
    }
}
