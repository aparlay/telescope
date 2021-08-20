<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Repositories\FollowRepository;
use App\Exceptions\BlockedException;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

class FollowService
{
    protected $followRepository;

    public function __construct()
    {
        $this->followRepository = new FollowRepository(new Follow());
    }

    /**
     * Responsible to create follow for given user
     *
     * @param User
     * @return Array
    */
    public function create(User $user)
    {
        $statusCode = Response::HTTP_OK;
        if (($follow = $this->followRepository->isFollowed($user)) === null) {
            $follow = $this->followRepository->create(['user' => ['_id' => new ObjectId($user->_id)]]);
            $statusCode = Response::HTTP_CREATED;
        }
        return ['data' => $follow, 'statusCode' => $statusCode];
    }

    /**
     * Responsible to unfollow the given user
     *
     * @param User
     * @return Array
    */
    public function unfollow(User $user)
    {
        if (($follow = $this->followRepository->isFollowed($user)) === null) {
            throw new BlockedException('No Record Found', null, null, Response::HTTP_NOT_FOUND);
        }
        $follow->delete();
    }
}
