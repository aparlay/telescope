<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CriticalPaymentErrorLogger extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public string $ref, public string $service, public string $message, public string $channel = '')
    {
        $this->channel = (! empty($channel) ? $channel : config('app.slack_payment_failed'));
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
        return (new SlackMessage())
            ->to($this->channel)
            ->from('Payment Monitoring System', ':rotating_light:')
            ->content('Critical Payment Error')
            ->attachment(function ($attachment) {
                $attachment->title('Open Admin area', $this->ref)
                    ->fields([
                        'Service' => $this->service,
                        'URL' => '`'.$this->url.'`',
                        'Request' => '```'.json_encode($this->req ?? []).'```',
                        'Response' => '```'.json_encode($this->res ?? []).'```',
                    ]);
            })
            ->error();
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
