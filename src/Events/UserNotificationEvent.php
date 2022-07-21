<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Api\V1\Resources\FollowResource;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Api\V1\Resources\UserResource;
use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\UserNotification;
use Aparlay\Payment\Api\V1\Resources\TipResource;
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
     * The credit card instance.
     *
     * @var UserNotification
     */
    public UserNotification $userNotification;
    public string $userId;
    public string $eventType;
    public $message;
    public $payload;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $userNotificationId, string $userId, string $message = '', array $payload = [], string $eventType = '')
    {
        $this->userNotification = UserNotification::findOrFail(new ObjectId($userNotificationId));
        $this->userId = $userId;
        $this->eventType = $eventType;
        $this->message = $message;
        $this->payload = $payload;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('users.'.$this->userId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'UserNotification.'.$this->eventType;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            '_id' => (string) $this->userNotification->_id,
            'category' => $this->userNotification->category,
            'category_label' => $this->userNotification->category_label,
            'status' => $this->userNotification->status,
            'status_label' => $this->userNotification->status_label,
            'message' => $this->message,
            'payload' => $this->payload,
            'created_at' => $this->userNotification->created_at->valueOf(),
            'updated_at' => $this->userNotification->updated_at->valueOf(),
        ];
    }
}
