<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class MediaScoreChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public User|Authenticatable $admin)
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
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $message = "Video {$notifiable->slack_admin_url} moderation ";
        $message .= "is getting done by {$this->admin->slack_admin_url}.";
        $message .= PHP_EOL.'_*Scores:*_ '.PHP_EOL;
        foreach ($notifiable->scores as $score) {
            $message .= PHP_EOL."- _*{$score['type']}: {$score['score']}*_ ";
        }
        if ($notifiable->status === MediaStatus::CONFIRMED->value) {
            $message .= PHP_EOL.PHP_EOL."- _*Public Feed Approval: Confirmed*_ ";
        }
        if ($notifiable->status === MediaStatus::DENIED->value) {
            $message .= PHP_EOL.PHP_EOL."- _*Public Feed Approval: Denied*_ ";
        }

        return (new SlackMessage())
            ->to(config('app.slack_video_pending'))
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
