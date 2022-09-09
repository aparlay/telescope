<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Admin\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class UserNotificationUnreadStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(
        private string $userId,
        private bool $previousHasUnreadNotification,
        private bool|null $hasUnreadNotification = null,
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
            'has_unread_notification' => $this->hasUnreadNotification(),
        ];
    }

    /**
     * @return bool
     */
    public function broadcastWhen(): bool
    {
        return $this->hasUnreadNotification() !== $this->previousHasUnreadNotification;
    }

    private function hasUnreadNotification(): bool
    {
        if (is_null($this->hasUnreadNotification)) {
            $user = User::find($this->userId);
            $this->hasUnreadNotification = $user->has_unread_notification;
        }

        return $this->hasUnreadNotification;
    }
}
