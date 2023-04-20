<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\UserNotification;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MongoDB\BSON\ObjectId;

class UserNotificationEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    use InteractsWithBroadcasting;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public string $userNotificationId,
        public string $userId,
        public string $message = '',
        public array $payload = [],
        public string $eventType = ''
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->userId);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'UserNotification.' . $this->eventType;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $userNotification = UserNotification::findOrFail(new ObjectId($this->userNotificationId));

        return [
            '_id' => (string) $userNotification->_id,
            'category' => $userNotification->category,
            'category_label' => $userNotification->category_label,
            'status' => $userNotification->status,
            'status_label' => $userNotification->status_label,
            'message' => $this->message,
            'payload' => $this->payload,
            'created_at' => $userNotification->created_at->valueOf(),
            'updated_at' => $userNotification->updated_at->valueOf(),
        ];
    }
}
