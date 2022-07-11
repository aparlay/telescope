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

class MediaLikedNotification extends Notification
{
    use Queueable;
    use UserNotificationArray;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User|Authenticatable $user, Media $media, $message)
    {
        $this->entity_type = Media::shortClassName();
        $this->entity_id = new ObjectId($media->_id);
        $this->user_id = new ObjectId($user->_id);
        $this->category = UserNotificationCategory::LIKES->value;
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->message = $message;
        $this->eventType = 'MediaLike';
        $this->payload = [
            'user' => [
                '_id' => (string) $user->_id,
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
            'media' => [
                '_id' => (string) $media->_id,
                'cover' => $media->cover_url,
            ],
        ];
    }
}
