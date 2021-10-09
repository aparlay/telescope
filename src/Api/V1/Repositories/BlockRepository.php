<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Admin\Http\Middleware\Authenticate;
use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class BlockRepository implements RepositoryInterface
{
    protected Block $model;

    public function __construct($model)
    {
        if (! ($model instanceof Block)) {
            throw new \InvalidArgumentException('$model should be of User type');
        }

        $this->model = $model;
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Create block.
     *
     * @param array $data
     * @return Block
     */
    public function create(array $data)
    {
        $creator = auth()->user();

        try {
            return Block::create([
                'user' => $data['user'],
                'creator' => ['_id' => new ObjectId($creator->_id)],
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    /**
     * Delete block.
     *
     * @param string $id
     * @return Void
     */
    public function delete($id)
    {
        $this->model->destroy($id);
    }

    public function find($id)
    {
        // TODO: Implement find() method.
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
