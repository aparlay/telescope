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
     * @param  Follow  $follow
     * @return void
     */
    public function creating(Follow $follow): void
    {
        $user = User::user($follow->user['_id'])->first();
        $creator = User::user($follow->creator['_id'])->first();

        $follow->user = [
            '_id' => new ObjectId($user->_id),
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];

        $follow->creator = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];
    }

    /**
     * Handle the Follow "created" event.
     *
     * @param  Follow  $follow
     * @return void
     */
    public function created(Follow $follow): void
    {
        $followersCount = Follow::user($follow->user['_id'])->count();
        $follow->userObj->follower_count = $followersCount;
        $follow->userObj->addToSet('followers', [
            '_id' => new ObjectId($follow->creator['_id']),
            'username' => $follow->creator['username'],
            'avatar' => $follow->creator['avatar'],
        ], 10);
        $follow->userObj->count_fields_updated_at = array_merge(
            $follow->userObj->count_fields_updated_at,
            ['followers' => DT::utcNow()]
        );
        $follow->userObj->save();

        $followingCount = Follow::creator($follow->creator['_id'])->count();
        $follow->creatorObj->following_count = $followingCount;
        $follow->creatorObj->addToSet('followings', [
            '_id' => new ObjectId($follow->user['_id']),
            'username' => $follow->user['username'],
            'avatar' => $follow->user['avatar'],
        ]);
        $follow->creatorObj->count_fields_updated_at = array_merge(
            $follow->creatorObj->count_fields_updated_at,
            ['followings' => DT::utcNow()]
        );
        $follow->creatorObj->save();

        // Reset the Redis cache
        $cacheKey = (new Follow())->getCollection().':creator:'.$follow->creator['_id'];
        Redis::del($cacheKey);
        Follow::cacheByUserId($follow->creator['_id']);
    }

    /**
     * Handle the Follow "deleted" event.
     *
     * @param  Follow  $follow
     * @return void
     */
    public function deleted(Follow $follow): void
    {
        $followersCount = Follow::user($follow->user['_id'])->count();
        $follow->userObj->follower_count = $followersCount;
        $follow->userObj->removeFromSet('followers', [
            '_id' => new ObjectId($follow->creator['_id']),
            'username' => $follow->creator['username'],
            'avatar' => $follow->creator['avatar'],
        ]);
        $follow->userObj->count_fields_updated_at = array_merge(
            $follow->userObj->count_fields_updated_at,
            ['followers' => DT::utcNow()]
        );
        $follow->userObj->save();

        $followingCount = Follow::creator($follow->creator['_id'])->count();
        $follow->creatorObj->following_count = $followingCount;
        $follow->creatorObj->removeFromSet('followings', [
            '_id' => new ObjectId($follow->user['_id']),
            'username' => $follow->user['username'],
            'avatar' => $follow->user['avatar'],
        ]);
        $follow->creatorObj->count_fields_updated_at = array_merge(
            $follow->creatorObj->count_fields_updated_at,
            ['followings' => DT::utcNow()]
        );
        $follow->creatorObj->save();

        // Reset the Redis cache
        $cacheKey = (new Follow())->getCollection().':creator:'.$follow->creator['_id'];
        Redis::del($cacheKey);
        Follow::cacheByUserId($follow->creator['_id']);

        Log::debug(Follow::class);
    }
}
