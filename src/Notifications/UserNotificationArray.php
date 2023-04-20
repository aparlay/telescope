<?php

namespace Aparlay\Core\Notifications;

use MongoDB\BSON\ObjectId;

trait UserNotificationArray
{
    public string|null $entity_type;
    public ObjectId|null $entity_id;
    public mixed $usernotifiable;
    public int $category;
    public string $category_label;
    public int $status;
    public string $status_label;
    public string $message;
    public array $payload;
    public string $eventType;
    public ObjectId $user_id;

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [UserNotificationChannel::class];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'user_id' => $this->user_id,
            'message' => $this->message,
            'payload' => $this->payload,
        ];
    }
}
