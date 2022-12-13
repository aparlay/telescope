<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Jobs\Email as EmailJob;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Enums\EmailStatus;
use Aparlay\Core\Models\Enums\EmailType;
use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use MongoDB\BSON\ObjectId;

class CreatorAccountApprovementEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public string $subject, public string $title, public string $body, public bool $isVerified)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */

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
     * @param  User  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable): SlackMessage
    {
        $data = [
            'to' => $notifiable->email,
            'user' => [
                '_id' => new ObjectId($notifiable->_id),
                'username' => $notifiable->username,
                'avatar' => $notifiable->avatar,
            ],
            'status' => EmailStatus::QUEUED->value,
            'type' => EmailType::ACCOUNT_VERIFICATION->value,
        ];

        $email = Email::create($data);

        /** Prepare email content and dispatch the job to schedule the email */
        $to = $notifiable->email;
        $type = Email::TEMPLATE_EMAIL_ACCOUNT_VERIFICATION;
        $payload = [
            'title' => $this->title,
            'body' => $this->body,
            'isVerified' => $this->isVerified,
        ];
        EmailJob::dispatch((string) $email->_id, $to, $this->subject, $type, $payload);

        // sending Slack notification
        $message = 'Content Creator Application Proceeded';
        $message .= PHP_EOL.'_*User:*_ '.$notifiable->slack_admin_url;
        $message .= PHP_EOL.'_*Email:*_ '.$notifiable->email;
        $message .= PHP_EOL.'_*Country:*_ '.$notifiable->country_label;

        return (new SlackMessage())
            ->to(config('app.slack_apply_for_verification'))
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
