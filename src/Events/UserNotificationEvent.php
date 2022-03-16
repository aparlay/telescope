<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\UserNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use MongoDB\BSON\ObjectId;

class UserNotificationEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use InteractsWithSockets;

    /**
     * The credit card instance.
     *
     * @var UserNotification
     */
    public UserNotification $userNotification;
    public string $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $userNotificationId, string $userId)
    {
        $this->userNotification = UserNotification::findOrFail(new ObjectId($userNotificationId));
        $this->userId = $userId;
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
    public function broadcastAs()
    {
        return 'UserNotification';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'category' => (string) $this->userNotification->category,
            'category_label' => (string) $this->userNotification->category_label,
            'status' => (string) $this->userNotification->status,
            'status_label' => (string) $this->userNotification->status_label,
            'entity' => (string) $this->entity,
            'created_at' => $this->userNotification->created_at->valueOf(),
            'updated_at' => $this->userNotification->updated_at->valueOf(),
        ];
    }
}
