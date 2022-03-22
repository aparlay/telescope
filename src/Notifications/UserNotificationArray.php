<?php

namespace Aparlay\Core\Notifications;

use MongoDB\BSON\ObjectId;

trait UserNotificationArray
{
    public string|null $usernotifiable_type;
    public ObjectId|null $usernotifiable_id;
    public mixed $usernotifiable;
    public int $category;
    public int $status;
    public string $message;
    public ObjectId $user_id;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [UserNotificationChannel::class];
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
            'usernotifiable_type' => $notifiable->usernotifiable_type,
            'usernotifiable_id' => $notifiable->usernotifiable_id,
            'category' => $notifiable->category,
            'status' => $notifiable->status,
            'user_id' => $notifiable->user_id,
            'message' => $notifiable->message,
        ];
    }
}
