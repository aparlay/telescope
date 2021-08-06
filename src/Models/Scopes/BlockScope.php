<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait BlockScope
{
    /**
     * @param ObjectId|string $creatorId
     */
    public function scopeCreator(Builder $query, ObjectId | string $creatorId): Builder
    {
        $creatorId = $creatorId instanceof ObjectId ? $creatorId : new ObjectId($creatorId);

        return $query->where('creator._id', $creatorId);
    }

    /**
     * @param ObjectId|string $userId
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user._id', $userId);
    }

    public function scopeIsDeleted(Builder $query): Builder
    {
        return $query->where('is_deleted', true);
    }

    public function scopeIsNotDeleted(Builder $query): Builder
    {
        return $query->where('is_deleted', false);
    }
}
