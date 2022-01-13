<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\BlockRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use App\Exceptions\BlockedException;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

class BlockService
{
    use HasUserTrait;

    protected BlockRepository $blockRepository;

    public function __construct()
    {
        $this->blockRepository = new BlockRepository(new Block());
    }

    /**
     * Responsible to create block for given user.
     *
     * @param  User  $user
     * @return array
     */
    public function create(User $user)
    {
        $statusCode = Response::HTTP_OK;
        $creator = $this->getUser();

        if (($block = $this->blockRepository->isBlocked($creator, $user)) === null) {
            $block = $this->blockRepository->create(['user' => ['_id' => new ObjectId($user->_id)]]);
            $statusCode = Response::HTTP_CREATED;
        }

        return ['data' => $block, 'statusCode' => $statusCode];
    }

    /**
     * Responsible to unblock the given user.
     *
     * @param  User  $user
     * @return array
     */
    public function unblock(User $user): array
    {
        $creator = $this->getUser();
        if (($block = $this->blockRepository->isBlocked($creator, $user)) !== null) {
            $this->blockRepository->delete($block->_id);
        }

        return [];
    }
}
