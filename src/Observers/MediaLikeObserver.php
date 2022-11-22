<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\UserNotification;
use Aparlay\Core\Notifications\MediaLikedNotification;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class MediaLikeObserver extends BaseModelObserver
{
    /**
     * Handle the MediaLike "created" event.
     *
     * @param  MediaLike  $mediaLike
     * @return void
     * @throws InvalidArgumentException
     */
    public function created($mediaLike): void
    {
        $media = $mediaLike->mediaObj;
        if ($media === null) {
            return;
        }
        $media->updateLikes();

        $media->notify(
            new MediaLikedNotification(
                $mediaLike->creatorObj,
                $media->creatorObj,
                $media,
                $media->likesNotificationMessage()
            )
        );

        $media->creatorObj->updateLikes();

        // Reset the Redis cache
        MediaLike::cacheByUserId($mediaLike->creator['_id'], true);
        $cacheKey = md5('media:'.$media->_id.':likedBy:'.$mediaLike->creator['_id']);
        Cache::store('octane')->delete($cacheKey);
    }

    /**
     * Handle the MediaLike "deleted" event.
     *
     * @param  MediaLike  $mediaLike
     * @return void
     * @throws InvalidArgumentException
     */
    public function deleted($mediaLike): void
    {
        $media = $mediaLike->mediaObj;
        if ($media === null) {
            return;
        }
        $media->updateLikes();
        $media->userObj->updateLikes();

        if ($media->like_count === 0) {
            UserNotification::query()->category(UserNotificationCategory::LIKES->value)->mediaEntity($media->_id)->delete();
        }

        // Reset the Redis cache
        MediaLike::cacheByUserId($mediaLike->creator['_id'], true);
        $cacheKey = md5('media:'.$media->_id.':likedBy:'.$mediaLike->creator['_id']);
        Cache::store('octane')->delete($cacheKey);
    }
}
