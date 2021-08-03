<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Follow;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait FollowScope
{
    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $creatorId
     * @return Builder
     */
    public function scopeCreator(Builder $query, ObjectId|string $creatorId): Builder
    {
        $creatorId = $creatorId instanceof ObjectId ? $creatorId : new ObjectId($creatorId);

        return $query->where('creator._id', $creatorId);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return Builder
     */
    public function scopeUser(Builder $query, ObjectId|string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user._id', $userId);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDeleted(Builder $query): Builder
    {
        return $query->where('is_deleted', true);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeNotDeleted(Builder $query): Builder
    {
        return $query->where('is_deleted', false);
    }

    /**
     * @param  Builder  $query
     * @param  int  $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, int $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', Follow::STATUS_ACCEPTED);
    }
}
