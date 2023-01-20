<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\UserNotification;
use Aparlay\Core\Notifications\MediaLikedNotification;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

use function Clue\StreamFilter\fun;

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
        $media->creatorObj->updateLikes();

        // Reset the Redis cache
        MediaLike::cacheByUserId($mediaLike->creator['_id'], true);
        $cacheKey = md5('media:'.$media->_id.':likedBy:'.$mediaLike->creator['_id']);
        Cache::store('octane')->delete($cacheKey);

        // no need to send notification when user is owner of the media
        if ((string) $media->creatorObj->_id !== (string) $mediaLike->creatorObj->_id) {
            $media->notify(
                new MediaLikedNotification(
                    $mediaLike->creatorObj,
                    $media->creatorObj,
                    $media,
                    $media->likesNotificationMessage()
                )
            );
        }
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

        // we don't show notification if there is no liker or the only liker is the owner itself
        $mediaLikes = MediaLike::query()
            ->media($media->_id)
            ->limit(2)
            ->get()
            ->filter(function ($mediaLike, $key) use ($media) {
                return (string) $mediaLike->creator['_id'] !== (string) $media->creator['_id'];
            })
            ->all();

        if (count($mediaLikes) === 0) {
            UserNotification::query()
                ->category(UserNotificationCategory::LIKES->value)
                ->mediaEntity($media->_id)
                ->update(['status' => UserNotificationStatus::INVISIBLE->value]);
        }

        // Reset the Redis cache
        MediaLike::cacheByUserId($mediaLike->creator['_id'], true);
        $cacheKey = md5('media:'.$media->_id.':likedBy:'.$mediaLike->creator['_id']);
        Cache::store('octane')->delete($cacheKey);
    }
}
