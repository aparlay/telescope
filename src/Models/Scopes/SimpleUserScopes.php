<?php

namespace Aparlay\Core\Models\Scopes;

use MongoDB\BSON\ObjectId;

trait SimpleUserScopes
{
    /**
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeCreator($query, $userId): mixed
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('creator._id', $userId);
    }

    /**
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeUser($query, $userId): mixed
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user._id', $userId);
    }
}
