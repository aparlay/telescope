<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserNotification;
use Aparlay\Core\Notifications\MediaCommentedNotification;
use Psr\SimpleCache\InvalidArgumentException;

class MediaCommentObserver extends BaseModelObserver
{
    /**
     * Handle the MediaComment "created" event.
     *
     * @param  MediaComment  $mediaComment
     * @return void
     * @throws InvalidArgumentException
     */
    public function created(MediaComment $mediaComment): void
    {
        $media = $mediaComment->mediaObj;
        if ($media === null) {
            return;
        }
        $media->updateComments();

        // do not create a separate notification for tip comment
        if (! empty($mediaComment->tip_id)) {
            return;
        }

        if (empty($mediaComment->reply_to_user['_id'])) {
            $media->notify(
                new MediaCommentedNotification(
                    $mediaComment->creatorObj,
                    $media->creatorObj,
                    $media,
                    $mediaComment,
                    $media->commentsNotificationMessage(),
                )
            );
        } else {
            $media->notify(
                new MediaCommentedNotification(
                    $mediaComment->creatorObj,
                    User::user($mediaComment->reply_to_user['_id'])->first(),
                    $media,
                    $mediaComment,
                    __(':username replied to your comment.', ['username' => $mediaComment->creator['username']])
                )
            );
        }
    }

    /**
     * Handle the MediaComment "deleted" event.
     *
     * @param  MediaComment  $model
     * @return void
     * @throws InvalidArgumentException
     */
    public function deleted($model): void
    {
        $media = $model->mediaObj;
        if ($media === null) {
            return;
        }
        $media->updateComments();

        if ($media->comment_count === 0) {
            UserNotification::query()
                ->category(UserNotificationCategory::COMMENTS->value)
                ->mediaEntity($media->_id)
                ->update(['status' => UserNotificationStatus::INVISIBLE->value]);
        }
    }
}
