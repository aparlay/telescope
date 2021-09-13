<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
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
        $this->model->user = $data['user'];
        $this->model->creator = ['_id' => new ObjectId($creator->_id)];
        $this->model->save();

        return $this->model;
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * Check if already blocked by the given user.
     *
     * @param User $user
     * @return Block|void
     */
    public function isBlocked(User $user)
    {
        $creator = auth()->user();

        return Block::user($user->_id)->creator($creator->_id)->first();
    }
}
