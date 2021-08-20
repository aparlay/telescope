<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Repositories\FollowRepository;

class FollowService
{
    /**
     * Responsible for check follower exist or not.
     *
     * @param User
     * @return Follow|void
     */
    public static function isfollowed(User $user)
    {
        return Follow::user($user->_id)->Creator(auth()->user()->_id)->first();
    }

    /**
     * Responsible for check follower exist or not.
     *
     * @param User
     * @return array
     */
    public static function followUser(User $user)
    {
        $follow = FollowRepository::findFollower($user);
        if (null === $follow) {
            $follow = FollowRepository::createFollower($user);

            return [
                'status' => false,
                'data' => $follow,
            ];
        }

        return [
            'status' => true,
            'data' => $follow,
        ];
    }
}
