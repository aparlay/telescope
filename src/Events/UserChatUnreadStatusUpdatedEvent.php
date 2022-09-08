<?php

namespace Aparlay\Core\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserChatUnreadStatusUpdatedEvent implements ShouldBroadcast
{
    public function __construct(
        private string $userId,
        private bool $oldStatus,
        private bool $newStatus
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
        return 'User.ChatUnreadStatusUpdated';
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'has_unread_chat' => $this->newStatus,
        ];
    }

    /**
     * @return bool
     */
    public function broadcastWhen(): bool
    {
        return $this->newStatus !== $this->oldStatus;
    }
}
