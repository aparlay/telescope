<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Api\V1\Models\UserNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class UserNotificationUnreadStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(
        private string $userId
    ) {
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
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
        return 'User.NotificationUnreadStatusUpdated';
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'has_unread_notification' => UserNotification::query()->user($this->userId)->notVisited()->exists(),
        ];
    }
}
