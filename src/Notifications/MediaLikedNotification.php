<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use MongoDB\BSON\ObjectId;

class MediaLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UserNotificationArray;

    /**
     * Create a new notification instance.
     *
     * @param mixed $message
     *
     * @return void
     */
    public function __construct(User|Authenticatable $actor, User|Authenticatable $receiver, Media $media, $message)
    {
        $this->entity_type    = Media::shortClassName();
        $this->entity_id      = new ObjectId($media->_id);
        $this->user_id        = new ObjectId($receiver->_id);
        $this->category       = UserNotificationCategory::LIKES->value;
        $this->category_label = UserNotificationCategory::LIKES->label();
        $this->status         = UserNotificationStatus::NOT_VISITED->value;
        $this->status_label   = UserNotificationStatus::NOT_VISITED->label();
        $this->message        = $message;
        $this->eventType      = 'MediaLike';
        $this->payload        = [
            'user' => [
                '_id' => (string) $actor->_id,
                'username' => $actor->username,
                'avatar' => $actor->avatar,
            ],
            'media' => [
                '_id' => (string) $media->_id,
                'cover' => $media->cover_url,
                'slug' => $media->slug,
            ],
        ];
    }
}
