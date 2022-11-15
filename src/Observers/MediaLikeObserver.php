<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\UserNotification;
use Aparlay\Core\Notifications\MediaLikedNotification;
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
        if (isset($media->likes[0]['username'], $media->likes[1]['username']) && $media->like_count > 2) {
            $message = __(':username1, :username2 and :count others liked your video.', ['username' => $media->likes[0]['username'], 'username2' => $media->likes[1]['username'], 'count' => $media->like_count - 2]);
        } elseif (isset($media->likes[0]['username'], $media->likes[1]['username']) && $media->like_count == 2) {
            $message = __(':username1 and :username2 liked your video.', ['username1' => $media->likes[0]['username'], 'username2' => $media->likes[1]['username']]);
        } else {
            $message = __(':username liked your video.', ['username' => $mediaLike->creator['username']]);
        }
        $media->notify(
            new MediaLikedNotification(
                $mediaLike->creatorObj,
                $media->creatorObj,
                $media,
                $message
            )
        );

        $media->creatorObj->updateLikes();

        // Reset the Redis cache
        MediaLike::cacheByUserId($mediaLike->creator['_id'], true);
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
    }
}
