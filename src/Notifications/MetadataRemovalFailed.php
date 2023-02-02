<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class MetadataRemovalFailed extends Notification
{
    use Queueable;

    public function __construct(public string $file)
    {
    }

    public function via($notifiable): array
    {
        return ['slack'];
    }

    public function toSlack($notifiable): SlackMessage
    {
        $message = 'Metadata removal failed for file ' . pathinfo($this->file, PATHINFO_FILENAME) . '.' . pathinfo($this->file, PATHINFO_EXTENSION);
        return (new SlackMessage())
            ->to(config('app.slack_job_error'))
            ->content($message)
            ->success();
    }
}
