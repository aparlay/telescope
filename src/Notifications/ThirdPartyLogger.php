<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ThirdPartyLogger extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public string $service, public string $url, public array $req, public array $res, public string $channel = '')
    {
        $this->channel = (! empty($channel) ? $channel : config('app.slack_third_party_logger'));
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
        $message = '[' . $this->service . '] ['.$this->url.']';
        $message .= PHP_EOL.'_*Request:*_ '.! empty($this->req) ? json_encode($this->req) : ' empty.';
        $message .= PHP_EOL.'_*Response:*_ '.! empty($this->res) ? json_encode($this->res) : ' empty.';

        return (new SlackMessage())
            ->to($this->channel)
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
