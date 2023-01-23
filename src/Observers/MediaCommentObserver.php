<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Resources\MediaCommentResource;
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
    public function deleted(MediaComment $model): void
    {
        MediaComment::query()->parent($model->_id)->delete();

        $parentObj = $model->parentObj;
        if ($parentObj) {
            if ($model->is_first) {
                $newFirstReply = MediaComment::query()
                    ->parent($parentObj->_id)
                    ->oldest('_id')
                    ->limit(1)
                    ->first();

                $parentObj->first_reply = null;

                if ($newFirstReply) {
                    $newFirstReply->is_first = true;
                    $newFirstReply->save();
                    $parentObj->first_reply = [
                        '_id' => (string) $newFirstReply->_id,
                        'parent_id' => $newFirstReply->parent ? (string) $newFirstReply->parent['_id'] : null,
                        'media_id' => (string) $newFirstReply->media_id,
                        'text' => $newFirstReply->text,
                        'likes_count' => $newFirstReply->likes_count ?? 0,
                        'user_id' => (string) $newFirstReply->user_id,
                        'username' => $newFirstReply->reply_to_user['username'] ?? null,
                        'creator' => $newFirstReply->creator,
                        'created_at' => $newFirstReply->created_at->valueOf()
                    ];
                }
            }

            if ($parentObj->replies_count > 0) {
                $parentObj->replies_count--;
            }
            $parentObj->save();
        }

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
