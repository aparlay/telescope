<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Repositories\BlockRepository;
use App\Exceptions\BlockedException;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

class BlockService
{
    protected $blockRepository;

    public function __construct()
    {
        $this->blockRepository = new BlockRepository(new Block());
    }

      /**
     * Responsible to create block for given user.
     *
     * @param User
     * @return array
     */
    public function create(User $user)
    {
        $statusCode = Response::HTTP_OK;
        if (($block = $this->blockRepository->isBlocked($user)) === null) {
            $block = $this->blockRepository->create(['user' => ['_id' => new ObjectId($user->_id)]]);
            $statusCode = Response::HTTP_CREATED;
        }

        return ['data' => $block, 'statusCode' => $statusCode];
    }

    /**
     * Responsible to unblock the given user.
     *
     * @param User
     * @return Bool
     */
    public function unblock(User $user)
    {
        if (($block = $this->blockRepository->isBlocked($user)) === null) {
            throw new BlockedException('No Record Found', null, null, Response::HTTP_NOT_FOUND);
        }
        $block->delete();
    }
}
