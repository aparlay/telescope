<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\MediaCommentedNotification;
use Psr\SimpleCache\InvalidArgumentException;

class MediaCommentObserver extends BaseModelObserver
{
    /**
     * Handle the MediaComment "created" event.
     *
     * @param  MediaComment  $model
     * @return void
     * @throws InvalidArgumentException
     */
    public function created($model): void
    {
        $media = $model->mediaObj;
        $commentCount = MediaComment::query()->media($media->_id)->count();
        $media->comment_count = $commentCount;
        $media->count_fields_updated_at = array_merge(
            $media->count_fields_updated_at,
            ['comments' => DT::utcNow()]
        );
        $media->save();
        if (empty($model->reply_to_user['_id'])) {
            $media->notify(
                new MediaCommentedNotification(
                    $model->creatorObj,
                    $media->creatorObj,
                    $media,
                    $model,
                    __(':username commented on your video.', ['username' => $model->creator['username']])
                )
            );
        } else {
            $media->notify(
                new MediaCommentedNotification(
                    $model->creatorObj,
                    User::find($model->reply_to_user['_id']),
                    $media,
                    $model,
                    __(':username replied to your comment.', ['username' => $model->creator['username']])
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
        $commentCount = MediaComment::query()->media($media->_id)->count();
        $media->comment_count = $commentCount;
        $media->count_fields_updated_at = array_merge(
            $media->count_fields_updated_at,
            ['comments' => DT::utcNow()]
        );
        $media->save();
    }
}
