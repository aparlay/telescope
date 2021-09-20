<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\User;
use MongoDB\BSON\ObjectId;

class FollowRepository implements RepositoryInterface
{
    protected Follow $model;

    public function __construct($model)
    {
        if (! ($model instanceof Follow)) {
            throw new \InvalidArgumentException('$model should be of Follow type');
        }

        $this->model = $model;
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Create Follow.
     *
     * @param array $data
     * @return Follow
     */
    public function create(array $data)
    {
        $creator = auth()->user();
        try {

            $this->model->user = $data['user'];
            $this->model->creator = ['_id' => new ObjectId($creator->_id)];
            $this->model->save();
            return $this->model;
        
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;

        }        
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
     * Check if already followed by the given user.
     *
     * @param User $user
     * @return Follow|void
     */
    public function isFollowed(User $user)
    {
        $creator = auth()->user();

        return Follow::user($user->_id)->creator($creator->_id)->first();
    }
}
