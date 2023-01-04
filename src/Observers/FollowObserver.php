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
        if (!isset($model->user['username'], $model->user['avatar'])) {
            $user = User::user($model->user['_id'])->first();
            $model->user = [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ];
        }

        if (!isset($model->creator['username'], $model->creator['avatar'])) {
            $creator = User::user($model->creator['_id'])->first();
            $model->creator = [
                '_id' => new ObjectId($creator->_id),
                'username' => $creator->username,
                'avatar' => $creator->avatar,
            ];
        }

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
        $userObj = $model->userObj;
        $creatorObj = $model->creatorObj;

        $followersCount = Follow::query()->user($model->user['_id'])->count();
        $userObj->addToSet('followers', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
        ], 10);

        if (! isset($userObj->count_fields_updated_at)) {
            $userObj->count_fields_updated_at = [];
        }

        $userObj->count_fields_updated_at = array_merge(
            $userObj->count_fields_updated_at,
            ['followers' => DT::utcNow()]
        );

        $stats = $userObj->stats;
        $stats['counters']['followers'] = $followersCount;
        $userObj->stats = $stats;
        $userObj->save();

        $followingCount = Follow::query()->creator($model->creator['_id'])->count();
        $creatorObj->addToSet('followings', [
            '_id' => new ObjectId($model->user['_id']),
            'username' => $model->user['username'],
            'avatar' => $model->user['avatar'],
        ]);
        $creatorObj->count_fields_updated_at = array_merge(
            $creatorObj->count_fields_updated_at,
            ['followings' => DT::utcNow()]
        );

        $stats = $creatorObj->stats;
        $stats['counters']['followings'] = $followingCount;
        $creatorObj->stats = $stats;
        $creatorObj->save();

        $userObj->notify(
            new UserFollowedNotification(
                $creatorObj,
                $userObj,
                __(':username started following you.', ['username' => $model->creator['username']])
            )
        );

        // Reset the Redis cache
        Follow::cacheByUserId($model->creator['_id'], true);
        $cacheKey = md5('user:'.$model->creator['_id'].':followedBy:'.$model->user['_id']);
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
        $userObj = $model->userObj;
        $creatorObj = $model->creatorObj;

        $followersCount = Follow::query()->user($model->user['_id'])->count();
        $userObj->removeFromSet('followers', [
            '_id' => new ObjectId($model->creator['_id']),
            'username' => $model->creator['username'],
            'avatar' => $model->creator['avatar'],
        ]);

        if (! isset($userObj->count_fields_updated_at)) {
            $userObj->count_fields_updated_at = [];
        }

        $userObj->count_fields_updated_at = array_merge(
            $userObj->count_fields_updated_at,
            ['followers' => DT::utcNow()]
        );

        $stats = $userObj->stats;
        $stats['counters']['followers'] = $followersCount;
        $userObj->stats = $stats;
        $userObj->save();

        $followingCount = Follow::query()->creator($model->creator['_id'])->count();
        $creatorObj->removeFromSet('followings', [
            '_id' => new ObjectId($model->user['_id']),
            'username' => $model->user['username'],
            'avatar' => $model->user['avatar'],
        ]);
        $creatorObj->count_fields_updated_at = array_merge(
            $creatorObj->count_fields_updated_at,
            ['followings' => DT::utcNow()]
        );
        $stats = $creatorObj->stats;
        $stats['counters']['followings'] = $followingCount;
        $creatorObj->stats = $stats;

        $creatorObj->save();

        // Reset the Redis cache
        Follow::cacheByUserId($model->creator['_id'], true);
        $cacheKey = md5('user:'.$model->creator['_id'].':followedBy:'.$model->user['_id']);
        Cache::store('octane')->delete($cacheKey);
    }
}
