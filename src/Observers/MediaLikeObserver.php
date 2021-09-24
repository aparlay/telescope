<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;

class MediaLikeObserver
{
    /**
     * Handle the MediaLike "creating" event.
     *
     * @param  MediaLike  $mediaLike
     * @return void
     */
    public function creating(MediaLike $mediaLike)
    {
        $creator = User::user($mediaLike->creator['_id'])->first();

        $mediaLike->creator = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];
    }

    /**
     * Handle the MediaLike "created" event.
     *
     * @param  MediaLike  $mediaLike
     * @return void
     */
    public function created(MediaLike $mediaLike)
    {
        $media = $mediaLike->mediaObj;
        $media->like_count++;
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

        $user = $media->userObj;
        $user->like_count++;
        $user->addToSet('likes', [
            '_id' => new ObjectId($mediaLike->creator['_id']),
            'username' => $mediaLike->creator['username'],
            'avatar' => $mediaLike->creator['avatar'],
        ], 10);
        $user->count_fields_updated_at = array_merge(
            $user->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );

        $user->save();

        // Reset the Redis cache
        $cacheKey = (new MediaLike())->getCollection() . ':creator:' . $mediaLike->creator['_id'];
        Redis::del($cacheKey);
        MediaLike::cacheByUserId($mediaLike->creator['_id']);
    }

    /**
     * Handle the MediaLike "deleted" event.
     *
     * @param  MediaLike  $mediaLike
     * @return void
     */
    public function deleted(MediaLike $mediaLike)
    {
        $media = $mediaLike->mediaObj;
        $media->like_count--;
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
        $user->like_count--;
        $user->removeFromSet('likes', [
            '_id' => new ObjectId($mediaLike->creator['_id']),
            'username' => $mediaLike->creator['username'],
            'avatar' => $mediaLike->creator['avatar'],
        ]);
        $user->count_fields_updated_at = array_merge(
            $user->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );
        $user->save();

        // Reset the Redis cache
        $cacheKey = (new MediaLike())->getCollection() . ':creator:' . $mediaLike->creator['_id'];
        Redis::del($cacheKey);
        MediaLike::cacheByUserId($mediaLike->creator['_id']);
    }
}
