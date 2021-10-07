<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;

class FollowObserver extends BaseModelObserver
{
    /**
     * Handle the Follow "creating" event.
     *
     * @param  Follow  $model
     * @return void
     */
    public function creating($model): void
    {
        $user = User::user($model->user['_id'])->first();
        $creator = User::user($model->creator['_id'])->first();

        $model->user = [
            '_id' => new ObjectId($user->_id),
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];

        $model->creator = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];

        parent::creating($model);
    }

    /**
     * Handle the Follow "created" event.
     *
     * @param  Follow  $model
     * @return void
     */
    public function created($model): void
    {
        $followersCount = Follow::user($model->user['_id'])->count();
        $model->userObj->follower_count = $followersCount;
        $model->userObj->addToSet('followers', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
        ], 10);
        $model->userObj->count_fields_updated_at = array_merge(
            $model->userObj->count_fields_updated_at,
            ['followers' => DT::utcNow()]
        );
        $model->userObj->save();

        $followingCount = Follow::creator($model->creator['_id'])->count();
        $model->creatorObj->following_count = $followingCount;
        $model->creatorObj->addToSet('followings', [
            '_id' => new ObjectId($model->user['_id']),
            'username' => $model->user['username'],
            'avatar' => $model->user['avatar'],
        ]);
        $model->creatorObj->count_fields_updated_at = array_merge(
            $model->creatorObj->count_fields_updated_at,
            ['followings' => DT::utcNow()]
        );
        $model->creatorObj->save();

        // Reset the Redis cache
        $cacheKey = (new Follow())->getCollection().':creator:'.$model->creator['_id'];
        Redis::del($cacheKey);
        Follow::cacheByUserId($model->creator['_id']);
    }

    /**
     * Handle the Follow "deleted" event.
     *
     * @param  Follow  $model
     * @return void
     */
    public function deleted($model): void
    {
        $followersCount = Follow::user($model->user['_id'])->count();
        $model->userObj->follower_count = $followersCount;
        $model->userObj->removeFromSet('followers', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
        ]);
        $model->userObj->count_fields_updated_at = array_merge(
            $model->userObj->count_fields_updated_at,
            ['followers' => DT::utcNow()]
        );
        $model->userObj->save();

        $followingCount = Follow::creator($model->creator['_id'])->count();
        $model->creatorObj->following_count = $followingCount;
        $model->creatorObj->removeFromSet('followings', [
            '_id' => new ObjectId($model->user['_id']),
            'username' => $model->user['username'],
            'avatar' => $model->user['avatar'],
        ]);
        $model->creatorObj->count_fields_updated_at = array_merge(
            $model->creatorObj->count_fields_updated_at,
            ['followings' => DT::utcNow()]
        );
        $model->creatorObj->save();

        // Reset the Redis cache
        $cacheKey = (new Follow())->getCollection().':creator:'.$model->creator['_id'];
        Redis::del($cacheKey);
        Follow::cacheByUserId($model->creator['_id']);

        Log::debug(Follow::class);
    }
}
