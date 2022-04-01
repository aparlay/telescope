<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;
use MongoDB\BSON\ObjectId;

class CreatorAccountApprovementNotification extends Notification
{
    use Queueable;
    use UserNotificationArray;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User|Authenticatable $user, $message, $payload)
    {
        $this->entity_type = User::shortClassName();
        $this->entity_id = new ObjectId($user->_id);
        $this->user_id = new ObjectId($user->_id);
        $this->category = UserNotificationCategory::SYSTEM->value;
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->message = $message;
        $this->payload = $payload;
        $this->eventType = 'CreatorAccountApprovement';
    }
}