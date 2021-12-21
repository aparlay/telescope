<?php

namespace Aparlay\Core\Models\Scopes;

use MongoDB\BSON\ObjectId;

trait UserDocumentScope
{
    use BaseScope;


    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $creatorId
     * @return Builder
     */
    public function scopeCreator(Builder $query, ObjectId | string $creatorId): Builder
    {
        $creatorId = $creatorId instanceof ObjectId ? $creatorId : new ObjectId($creatorId);

        return $query->where('creator._id', $creatorId);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return Builder
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user._id', $userId);
    }

}
