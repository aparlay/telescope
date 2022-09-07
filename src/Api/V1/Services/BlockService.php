<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\BlockRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Events\UserBlockedEvent;
use App\Exceptions\BlockedException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class BlockService
{
    use HasUserTrait;

    public function __construct()
    {
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

        if (($block = Block::query()->user($user->_id)->creator($creator->_id)->first()) === null) {
            $block = Block::create([
                'user' => [
                    '_id'      => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar'   => $user->avatar,
                ],
                'creator' => [
                    '_id'      => new ObjectId($creator->_id),
                    'username' => $creator->username,
                    'avatar'   => $creator->avatar,
                ],
            ]);

            UserBlockedEvent::dispatch((string) $creator->_id, (string) $user->_id);
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
        if (($block = Block::query()->user($user->_id)->creator($creator->_id)->first()) !== null) {
            $block->delete();
        }

        return [];
    }
}
