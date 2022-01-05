<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Enums\AlertStatus;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait AlertScope
{
    use BaseScope;

    public function scopeVisited(Builder $query): Builder
    {
        return $query->where('status', AlertStatus::VISITED->value);
    }

    public function scopeNotVisited(Builder $query): Builder
    {
        return $query->where('status', AlertStatus::NOT_VISITED->value);
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
