<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\User;
use Aparlay\Core\Notifications\UserFollowedNotification;
use Illuminate\Support\Facades\Cache;
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
        $followersCount = Follow::query()->user($model->user['_id'])->count();
        $model->userObj->addToSet('followers', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
        ], 10);
        $model->userObj->count_fields_updated_at = array_merge(
            $model->userObj->count_fields_updated_at,
            ['followers' => DT::utcNow()]
        );

        $stats = $model->userObj->stats;
        $stats['counters']['followers'] = $followersCount;
        $model->userObj->stats = $stats;
        $model->userObj->save();

        $followingCount = Follow::query()->creator($model->creator['_id'])->count();
        $model->creatorObj->addToSet('followings', [
            '_id' => new ObjectId($model->user['_id']),
            'username' => $model->user['username'],
            'avatar' => $model->user['avatar'],
        ]);
        $model->creatorObj->count_fields_updated_at = array_merge(
            $model->creatorObj->count_fields_updated_at,
            ['followings' => DT::utcNow()]
        );

        $stats = $model->creatorObj->stats;
        $stats['counters']['followings'] = $followingCount;
        $model->creatorObj->stats = $stats;
        $model->creatorObj->save();

        $model->userObj->notify(
            new UserFollowedNotification(
                $model->creatorObj,
                $model->userObj,
                __(':username started following you.', ['username' => $model->creator['username']])
            )
        );

        // Reset the Redis cache
        Follow::cacheByUserId($model->creator['_id'], true);
        $cacheKey = 'user:'.$model->creator['_id'].':followedBy:'.$model->user['_id'];
        Cache::store('octane')->delete($cacheKey);
    }

    /**
     * Handle the Follow "deleted" event.
     *
     * @param  Follow  $model
     * @return void
     */
    public function deleted($model): void
    {
        $followersCount = Follow::query()->user($model->user['_id'])->count();
        $model->userObj->removeFromSet('followers', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
        ]);

        $model->userObj->count_fields_updated_at = array_merge(
            $model->userObj->count_fields_updated_at,
            ['followers' => DT::utcNow()]
        );

        $stats = $model->userObj->stats;
        $stats['counters']['followers'] = $followersCount;
        $model->userObj->stats = $stats;
        $model->userObj->save();

        $followingCount = Follow::query()->creator($model->creator['_id'])->count();
        $model->creatorObj->removeFromSet('followings', [
            '_id' => new ObjectId($model->user['_id']),
            'username' => $model->user['username'],
            'avatar' => $model->user['avatar'],
        ]);
        $model->creatorObj->count_fields_updated_at = array_merge(
            $model->creatorObj->count_fields_updated_at,
            ['followings' => DT::utcNow()]
        );
        $stats = $model->creatorObj->stats;
        $stats['counters']['followings'] = $followingCount;
        $model->creatorObj->stats = $stats;

        $model->creatorObj->save();

        // Reset the Redis cache
        Follow::cacheByUserId($model->creator['_id'], true);
        $cacheKey = 'user:'.$model->creator['_id'].':followedBy:'.$model->user['_id'];
        Cache::store('octane')->delete($cacheKey);
    }
}
