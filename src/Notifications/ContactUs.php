<?php

namespace Aparlay\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ContactUs extends Notification
{
    use Queueable;

    public string $email;
    public string $name;
    public string $subject;
    public string $message;
    public int $tried;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $email, string $name, string $subject, string $message)
    {
        $this->email = $email;
        $this->name = $name;
        $this->subject = $subject;
        $this->message = $message;
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
        $message = 'Contact us notification!!!';
        $message .= PHP_EOL.'_*Email:*_ '.$this->email;
        $message .= PHP_EOL.'_*Name:*_ '.$this->name;
        $message .= PHP_EOL.'_*Subject:*_ '.$this->subject;
        $message .= PHP_EOL.'_*Message:*_ '.$this->message;

        return (new SlackMessage())
            ->to(config('app.slack_contact_us'))
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
