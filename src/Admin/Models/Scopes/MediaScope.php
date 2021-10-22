<?php

namespace Aparlay\Core\Admin\Models\Scopes;

use Aparlay\Core\Admin\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

trait MediaScope
{
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $mediaId
     * @return Builder
     */
    public function scopeMedia(Builder $query, ObjectId | string $mediaId): Builder
    {
        $mediaId = $mediaId instanceof ObjectId ? $mediaId : new ObjectId($mediaId);

        return $query->where('_id', $mediaId);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', Media::VISIBILITY_PUBLIC);
    }

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('visibility', Media::VISIBILITY_PRIVATE);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', Media::STATUS_COMPLETED);
    }
}
