<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\MediaLikedNotification;
use MongoDB\BSON\ObjectId;
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
        $likeCount = MediaLike::media($media->_id)->count();
        $media->like_count = $likeCount;
        $media->addToSet('likes', [
            '_id' => new ObjectId($mediaLike->creator['_id']),
            'username' => $mediaLike->creator['username'],
            'avatar' => $mediaLike->creator['avatar'],
        ], 10);
        $media->count_fields_updated_at = array_merge(
            $media->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );
        $media->save();
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

        $user = $media->userObj;
        $likeCount = MediaLike::user($user->_id)->count();
        $user->like_count = $likeCount;
        $user->addToSet('likes', [
            '_id' => new ObjectId($mediaLike->creator['_id']),
            'username' => $mediaLike->creator['username'],
            'avatar' => $mediaLike->creator['avatar'],
        ], 10);
        $user->count_fields_updated_at = array_merge(
            $user->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );

        $stats = $user->stats;
        $stats['counters']['likes'] = $likeCount;
        $user->stats = $stats;

        $user->save();

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
        $likeCount = MediaLike::media($media->_id)->count();
        $media->like_count = $likeCount;
        $media->removeFromSet('likes', [
            '_id' => new ObjectId($mediaLike->creator['_id']),
            'username' => $mediaLike->creator['username'],
            'avatar' => $mediaLike->creator['avatar'],
        ]);
        $media->count_fields_updated_at = array_merge(
            $media->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );
        $media->save();

        $user = $media->userObj;
        $likeCount = MediaLike::user($user->_id)->count();
        $user->like_count = $likeCount;
        $user->removeFromSet('likes', [
            '_id' => new ObjectId($mediaLike->creator['_id']),
            'username' => $mediaLike->creator['username'],
            'avatar' => $mediaLike->creator['avatar'],
        ]);
        $user->count_fields_updated_at = array_merge(
            $user->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );

        $stats = $user->stats;
        $stats['counters']['likes'] = $likeCount;
        $user->stats = $stats;
        $user->save();

        // Reset the Redis cache
        MediaLike::cacheByUserId($mediaLike->creator['_id'], true);
    }
}
