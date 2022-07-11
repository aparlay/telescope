<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
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
    public function __construct(User|Authenticatable $follower, User|Authenticatable $followee, $message)
    {
        $this->entity_type = User::shortClassName();
        $this->entity_id = new ObjectId($followee->_id);
        $this->user_id = new ObjectId($follower->_id);
        $this->category = UserNotificationCategory::FOLLOWS->value;
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->message = $message;
        $this->eventType = 'UserFollowed';
        $this->payload = [
            'follower' => [
                '_id' => (string) $follower->_id,
                'username' => $follower->username,
                'avatar' => $follower->avatar,
            ],
            'followee' => [
                '_id' => (string) $followee->_id,
                'username' => $followee->username,
                'cover' => $followee->avatar,
            ]
        ];
    }
}
