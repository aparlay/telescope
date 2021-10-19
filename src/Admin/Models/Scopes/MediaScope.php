<?php

namespace Aparlay\Core\Admin\Models\Scopes;

use Aparlay\Core\Admin\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;

trait MediaScope
{
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeSortBy($query, $sorts): mixed
    {
        foreach ($sorts as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }

    public function scopeFilter($query, $filters)
    {
        foreach ($filters as $key => $filter) {
            if (is_numeric($filter)) {
                $query->where($key, (int)$filter);
            } else {
                $query->where($key, 'regex', new Regex('^' . $filter));
            }
        }

        return $query;
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
}
