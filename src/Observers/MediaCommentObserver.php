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

        // no need to send notification when user is owner of the media
        if ((string) $media->creatorObj->_id !== (string) $mediaComment->creatorObj->_id) {
            if (empty($mediaComment->reply_to_user['_id'])) {
                $receive = $media->creatorObj;
                $message = $media->commentsNotificationMessage();
            } else {
                $receive = User::user($mediaComment->reply_to_user['_id'])->first();
                $message = __(':username replied to your comment.', ['username' => $mediaComment->creator['username']]);
            }

            $media->notify(
                new MediaCommentedNotification(
                    $mediaComment->creatorObj,
                    $receive,
                    $media,
                    $mediaComment,
                    $message
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

        // we don't show notification if there is no commenter or the only commenter is the owner itself
        $lastMediaComment = MediaComment::query()
            ->with('creatorObj')
            ->media($media->_id)
            ->whereIdNeq($media->creator['_id'], 'created_by')
            ->recent()
            ->first();

        if ($lastMediaComment === null) {
            UserNotification::query()
                ->category(UserNotificationCategory::COMMENTS->value)
                ->mediaEntity($media->_id)
                ->update(['status' => UserNotificationStatus::INVISIBLE->value]);
        } else {
            UserNotification::query()
                ->category(UserNotificationCategory::COMMENTS->value)
                ->mediaEntity($media->_id)
                ->update([
                    'message' => $media->commentsNotificationMessage(),
                    'payload.user' => $lastMediaComment->creator
                ]);
        }
    }
}
