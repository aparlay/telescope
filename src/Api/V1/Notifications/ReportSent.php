<?php

namespace Aparlay\Core\Api\V1\Notifications;

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
        //
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
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $message = $notifiable->creator->slack_admin_url ?? 'A Guest user';
        $message .= ' reported ' . $notifiable->slack_subject_admin_url;
        $message .= PHP_EOL . '_*Reason:*_ ' . $notifiable->reason;

        return (new SlackMessage())
            ->to('waptap-testing')
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
            'title' => 'test',
            'description' => 'd',
        ];
    }
}
