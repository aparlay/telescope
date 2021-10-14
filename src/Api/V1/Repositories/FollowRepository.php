<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Follow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
     * @param  ObjectId|string  $creatorId
     * @param  ObjectId|string  $userId
     * @return Follow|Builder|Model|object|null
     */
    public function getFollow(ObjectId|string $creatorId, ObjectId|string $userId): Follow|Builder|null
    {
        return Follow::creator($creatorId)->user($userId)->first();
    }

    /**
     * Check if already followed by the given user.
     *
     * @param  ObjectId|string  $creatorId
     * @param  ObjectId|string  $userId
     * @return bool
     */
    public function isFollowed(ObjectId|string $creatorId, ObjectId|string $userId): bool
    {
        return (bool) Follow::creator($creatorId)->user($userId)->first();
    }
}
