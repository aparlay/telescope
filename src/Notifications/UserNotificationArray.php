<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\User;
use Kutia\Larafirebase\Messages\FirebaseMessage;
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
            'category_label' => $this->category_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'user_id' => $this->user_id,
            'message' => $this->message,
            'payload' => $this->payload,
        ];
    }

    /**
     * Get the firebase representation of the notification.
     */
    public function toFirebase($notifiable)
    {
        $user = User::findOrFail($this->user_id);

        return (new FirebaseMessage())
            ->withTitle('New notification')
            ->withBody($this->message)
            ->asNotification($user->setting['fcm_tokens']); // OR ->asMessage($deviceTokens);
    }
}
