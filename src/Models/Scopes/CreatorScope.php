<?php

namespace Aparlay\Core\Models\Scopes;

use MongoDB\BSON\ObjectId;

trait CreatorScope
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

}
