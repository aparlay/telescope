<?php

namespace Aparlay\Core\Notifications;

use MongoDB\BSON\ObjectId;

trait UserNotificationArray
{
    public string|null $entity_type;
    public ObjectId|null $entity_id;
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
    public function toArray(mixed $notifiable): array
    {
        return [
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'category' => $this->category,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'message' => $this->message,
        ];
    }
}
