<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\User;
use MongoDB\BSON\ObjectId;

class FollowObserver extends BaseModelObserver
{
    /**
     * Handle the Block "creating" event.
     *
     * @param  Follow  $block
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
        $follow->userObj->follower_count++;
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

        $follow->creatorObj->following_count++;
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
    }

    /**
     * Handle the Follow "deleted" event.
     *
     * @param  Follow  $follow
     * @return void
     */
    public function deleted(Follow $follow): void
    {
        $follow->userObj->follower_count--;
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

        $follow->creatorObj->following_count--;
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
    }
}
