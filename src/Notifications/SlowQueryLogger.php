<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SlowQueryLogger extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public array $queryLog = [], public string $channel = '')
    {
        $this->channel = (!empty($channel) ? $channel : config('app.slack_slow_query'));
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
        return (new SlackMessage())
            ->to($this->channel)
            ->content('Slow query')
            ->attachment(function ($attachment) {
                $attachment->title('Slow query details')
                    ->fields([
                        'Query Log' => '```' . json_encode($this->queryLog ?? []) . '```',
                    ]);
            })
            ->info();
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
