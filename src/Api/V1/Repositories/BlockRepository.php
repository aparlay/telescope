<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Events\UserBlockedEvent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class BlockRepository
{
    protected Block $model;

    public function __construct($model)
    {
        if (! ($model instanceof Block)) {
            throw new \InvalidArgumentException('$model should be of User type');
        }

        $this->model = $model;
    }

    /**
     * Create block.
     *
     * @param array $data
     * @return Block
     */
    public function create($data)
    {
        $creator = auth()->user();

        try {
            $block = Block::create([
                'user' => $data['user'],
                'creator' => ['_id' => new ObjectId($creator->_id)],
            ]);

            UserBlockedEvent::dispatch((string)$creator->_id, (string)$data['user']['_id']);

            return $block;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    /**
     * Delete block.
     *
     * @param string $id
     * @return void
     */
    public function delete($id)
    {
        $this->model->destroy($id);
    }

    /**
     * Check if already blocked by the given user.
     *
     * @param User|Authenticatable $creator
     * @param User $user
     * @return Block|null
     */
    public function isBlocked(User|Authenticatable $creator, User $user): ?Block
    {
        return Block::user($user->_id)->creator($creator->_id)->first();
    }
}
