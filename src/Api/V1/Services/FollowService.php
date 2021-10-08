<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\FollowRepository;
use App\Exceptions\BlockedException;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

class FollowService
{
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
        $creator = auth()->user();
        if (($follow = $this->followRepository->isFollowed($creator, $user)) === null) {
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
        $creator = auth()->user();
        if (($follow = $this->followRepository->isFollowed($creator, $user)) !== null) {
            $follow->delete();
        }

        return [];
    }
}
