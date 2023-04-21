<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class JobFailed extends Notification
{
    use Queueable;
    public string $job;
    public int $tried;
    public string $exception;
    public string $channel;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $job, int $tried, string $exception, string $channel = '')
    {
        $this->job       = $job;
        $this->tried     = $tried;
        $this->exception = $exception;
        $this->channel   = (!empty($channel) ? $channel : config('app.slack_job_error'));
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
        $message = $this->job . ' failed after ' . $this->tried . ' attempts.';
        $message .= PHP_EOL . '_*Exceptions:*_ ' . !empty($this->exception) ? $this->exception : ' attempts done.';

        return (new SlackMessage())
            ->to($this->channel)
            ->content($message)
            ->success();
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [

        ];
    }
}
