<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaComment;
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
        $media->notify(
            new MediaCommentedNotification(
                $model->creatorObj,
                $media,
                $model,
                __(':username write a comment on your media.', ['username' => $model->creator['username']])
            )
        );
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
