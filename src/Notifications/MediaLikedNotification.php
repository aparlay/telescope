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
    public function __construct(User|Authenticatable $user, Media $media, string $message)
    {
        $this->entity_type = Media::class;
        $this->entity_id = new ObjectId($media->_id);
        $this->user_id = new ObjectId($user->_id);
        $this->category = UserNotificationCategory::LIKES->value;
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->message = $message;
    }
}
