<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CreditCardVerified extends Notification
{
    use Queueable;

    public string $userId;
    public array $data;
    public string $mid;
    public int $tried;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $userId, string $data, string $mid, int $tried)
    {
        $this->userId = $userId;
        $this->data = $data;
        $this->mid = $mid;
        $this->tried = $tried;
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
        $message = 'New credit card has been verified after '.$this->tried.' attempts.';
        $message .= PHP_EOL.'_*User:*_ '.User::user($this->userId)->slack_admin_url;
        $message .= PHP_EOL.'_*Processing Mid:*_ '.$this->mid;
        foreach ($this->data as $key => $value) {
            $message .= PHP_EOL.'_*'.$key.':*_ '.$value;
        }

        return (new SlackMessage())
            ->to(config('payment.slack_channels.credit_card.success'))
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
