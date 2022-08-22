<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\MediaCommentedNotification;
use MongoDB\BSON\ObjectId;
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
        $commentCount = MediaComment::query()->media($media->_id)->count();
        $media->comment_count = $commentCount;
        $media->addToSet('comments', [
            '_id' => new ObjectId($mediaComment->creator['_id']),
            'username' => $mediaComment->creator['username'],
            'avatar' => $mediaComment->creator['avatar'],
        ], 10);
        $media->count_fields_updated_at = array_merge(
            $media->count_fields_updated_at,
            ['comments' => DT::utcNow()]
        );
        $media->save();
        if (empty($mediaComment->reply_to_user['_id'])) {
            if ($media->comment_count > 2 && ! isset($media->comments[1]['username'])) {
                $message = __(':username1, :username2 and :count others commented on your video.', ['username1' => $mediaComment->creator['username'], 'username2' => $media->comments[1]['username'], 'count' => $media->comments]);
            } elseif ($media->comment_count == 2 && ! isset($media->comments[1]['username'])) {
                $message = __(':username1 and :username2 commented on your video.', ['username1' => $mediaComment->creator['username'], 'username2' => $media->comments[1]['username']]);
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
        $commentCount = MediaComment::query()->media($media->_id)->count();
        $media->comment_count = $commentCount;
        $media->count_fields_updated_at = array_merge(
            $media->count_fields_updated_at,
            ['comments' => DT::utcNow()]
        );
        $media->save();
    }
}
