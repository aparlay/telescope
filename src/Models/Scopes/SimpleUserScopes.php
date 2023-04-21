<?php

namespace Aparlay\Core\Models\Scopes;

use MongoDB\BSON\ObjectId;

trait SimpleUserScopes
{
    public function scopeCreator($query, $userId): mixed
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('creator._id', $userId);
    }

    public function scopeUser($query, $userId): mixed
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user._id', $userId);
    }
}
