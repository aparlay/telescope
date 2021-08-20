<?php

namespace Aparlay\Core\Repositories;

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

    public function create(array $data)
    {
        // TODO: Implement create() method.
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
     * find user by email.
     *
     * @param string $email
     * @return Follow|void
     */
    public static function findFollower(User $user)
    {
        return Follow::user($user->_id)->creator(auth()->user()->_id)->first();
    }

    /**
     * find user by email.
     *
     * @param User $user
     * @return Follow
     */
    public function followerUser(User $user)
    {
        $modal = new Follow([
            'user' => ['_id' => new ObjectId($user->_id)],
            'creator' => ['_id' => new ObjectId(auth()->user()->_id)],
        ]);
        $modal->save();

        return $modal;
    }

    /**
     * find user by email.
     *
     * @param User $user
     * @return Follow
     */
    public static function createFollower(User $user)
    {
        $modal = new Follow([
            'user' => ['_id' => new ObjectId($user->_id)],
            'creator' => ['_id' => new ObjectId(auth()->user()->_id)],
        ]);
        $modal->save();

        return $modal;
    }
}
