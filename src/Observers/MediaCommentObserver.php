<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
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
            if (isset($media->comments[0]['username'], $media->comments[1]['username']) && $media->comment_count > 2) {
                $message = __(':username1, :username2 and :count others commented on your video.', ['username1' => $media->comments[0]['username'], 'username2' => $media->comments[1]['username'], 'count' => $media->comment_count - 2]);
            } elseif (isset($media->comments[0]['username'], $media->comments[1]['username']) && $media->comment_count == 2) {
                $message = __(':username1 and :username2 commented on your video.', ['username1' => $media->comments[0]['username'], 'username2' => $media->comments[1]['username']]);
            } else {
                $message = __(':username commented on your video.', ['username' => $mediaComment->creator['username']]);
            }

            $media->notify(
                new MediaCommentedNotification(
                    $mediaComment->creatorObj,
                    $media->creatorObj,
                    $media,
                    $mediaComment,
                    $message
                )
            );
        } else {
            $media->notify(
                new MediaCommentedNotification(
                    $mediaComment->creatorObj,
                    User::find($mediaComment->reply_to_user['_id']),
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
            UserNotification::query()->category(UserNotificationCategory::COMMENTS->value)->mediaEntity($media->_id)->delete();
        }
    }
}
