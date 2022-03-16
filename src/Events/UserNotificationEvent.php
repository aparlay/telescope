<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\UserNotification;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MongoDB\BSON\ObjectId;

class UserNotificationEvent implements  ShouldBroadcastNow
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

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $userNotificationId, string $userId)
    {
        $this->userNotification = UserNotification::findOrFail(new ObjectId($userNotificationId));
        $this->userId = $userId;
        $this->broadcastVia('socket');
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
        return 'UserNotification';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'category' => (string) $this->userNotification->category,
            'category_label' => (string) $this->userNotification->category_label,
            'status' => (string) $this->userNotification->status,
            'status_label' => (string) $this->userNotification->status_label,
            'entity' => (string) $this->userNotification->usernotifiable,
            'created_at' => $this->userNotification->created_at->valueOf(),
            'updated_at' => $this->userNotification->updated_at->valueOf(),
        ];
    }
}
