<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
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
            return Follow::create([
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
     * Delete Follow.
     *
     * @param string $id
     * @return void
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
     * Check if already followed by the given user.
     *
     * @param  User|Authenticatable  $creator
     * @param  User  $user
     * @return Follow|null
     */
    public function isFollowed(User|Authenticatable $creator, User $user): ?Follow
    {
        return Follow::user($user->_id)->creator($creator->_id)->first();
    }
}
