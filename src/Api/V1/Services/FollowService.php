<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\FollowRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

class FollowService
{
    use HasUserTrait;

    public function __construct()
    {
    }

    /**
     * Responsible to create follow for given user.
     *
     * @param  User  $user
     * @return array
     */
    public function follow(User $user): array
    {
        $statusCode = Response::HTTP_OK;
        $creator = $this->getUser();
        if (($follow = Follow::query()->creator($creator->_id)->user($user->_id)->first()) === null) {
            $follow = Follow::create([
                'user' => ['_id' => new ObjectId($user->_id)],
                'creator' => ['_id' => new ObjectId($creator->_id)],
            ]);

            $statusCode = Response::HTTP_CREATED;
        }

        return ['data' => $follow, 'statusCode' => $statusCode];
    }

    /**
     * Responsible to unfollow the given user.
     *
     * @param  User  $user
     * @return array
     */
    public function unfollow(User $user): array
    {
        $creator = $this->getUser();
        if (($follow = Follow::query()->creator($creator->_id)->user($user->_id)->first()) !== null) {
            $follow->delete();
        }

        return [];
    }
}
