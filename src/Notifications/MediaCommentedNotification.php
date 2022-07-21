<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;
use MongoDB\BSON\ObjectId;

class MediaCommentedNotification extends Notification
{
    use Queueable;
    use UserNotificationArray;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User|Authenticatable $user, Media $media, MediaComment $comment, $message)
    {
        $this->entity_type = Media::shortClassName();
        $this->entity_id = new ObjectId($media->_id);
        $this->user_id = new ObjectId($user->_id);
        $this->category = UserNotificationCategory::COMMENTS->value;
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->message = $message;
        $this->eventType = 'MediaComment';
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
            'comment' => [
                '_id' => (string) $comment->_id,
                'text' => $comment->text,
            ],
        ];
    }
}
