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
    protected FollowRepository $followRepository;

    public function __construct()
    {
        $this->followRepository = new FollowRepository(new Follow());
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
        if (($follow = $this->followRepository->getFollow($creator->_id, $user->_id)) === null) {
            $follow = $this->followRepository->create(['user' => ['_id' => new ObjectId($user->_id)]]);
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
        if (($follow = $this->followRepository->getFollow($creator->_id, $user->_id)) !== null) {
            $this->followRepository->delete($follow->_id);
        }

        return [];
    }
}
