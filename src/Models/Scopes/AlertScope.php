<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Alert;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait AlertScope
{
    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeVisited(Builder $query): Builder
    {
        return $query->where('status', Alert::STATUS_VISITED);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeNotVisited(Builder $query): Builder
    {
        return $query->where('status', Alert::STATUS_NOT_VISITED);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $mediaId
     * @return Builder
     */
    public function scopeMedia(Builder $query, ObjectId | string $mediaId): Builder
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);

        return $query->where('media_id', $mediaId);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return Builder
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }
}