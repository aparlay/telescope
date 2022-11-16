<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;
use MongoDB\BSON\ObjectId;

class UserFollowedNotification extends Notification
{
    use Queueable;
    use UserNotificationArray;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User|Authenticatable $actor, User|Authenticatable $receiver, $message)
    {
        $this->entity_type = User::shortClassName();
        $this->entity_id = new ObjectId($actor->_id);
        $this->user_id = new ObjectId($receiver->_id);
        $this->category = UserNotificationCategory::FOLLOWS->value;
        $this->category_label = UserNotificationCategory::FOLLOWS->label();
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->status_label = UserNotificationStatus::NOT_VISITED->label();
        $this->message = $message;
        $this->eventType = 'UserFollowed';
        $this->payload = [
            'user' => [
                '_id' => (string) $actor->_id,
                'username' => $actor->username,
                'avatar' => $actor->avatar,
            ],
        ];
    }
}
