<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;

class MediaLikeObserver extends BaseModelObserver
{
    /**
     * Handle the MediaLike "creating" event.
     *
     * @param  MediaLike  $model
     * @return void
     */
    public function creating($model): void
    {
        $creator = User::user($model->creator['_id'])->first();

        $model->creator = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];

        parent::creating($model);
    }

    /**
     * Handle the MediaLike "created" event.
     *
     * @param  MediaLike  $model
     * @return void
     */
    public function created($model): void
    {
        $media = $model->mediaObj;
        $likeCount = MediaLike::media($media->_id)->count();
        $media->like_count = $likeCount;
        $media->addToSet('likes', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
        ], 10);
        $media->count_fields_updated_at = array_merge(
            $media->count_fields_updated_at,
            ['likes' => DT::utcNow()]
        );
        $media->save();

        $user = $media->userObj;
        $likeCount = MediaLike::user($user->_id)->count();
        $user->like_count = $likeCount;
        $user->addToSet('likes', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
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
        $cacheKey = (new MediaLike())->getCollection().':creator:'.$model->creator['_id'];
        Redis::del($cacheKey);
        MediaLike::cacheByUserId($model->creator['_id']);
    }

    /**
     * Handle the MediaLike "deleted" event.
     *
     * @param  MediaLike  $model
     * @return void
     */
    public function deleted($model): void
    {
        $media = $model->mediaObj;
        $likeCount = MediaLike::media($media->_id)->count();
        $media->like_count = $likeCount;
        $media->removeFromSet('likes', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
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
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
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
        $cacheKey = (new MediaLike())->getCollection().':creator:'.$model->creator['_id'];
        Redis::del($cacheKey);
        MediaLike::cacheByUserId($model->creator['_id']);
    }
}
