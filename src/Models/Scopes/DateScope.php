<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\UTCDateTime;

trait DateScope
{
    public function scopeDate(Builder $query, ?UTCDateTime $start = null, ?UTCDateTime $end = null, string $field = 'created_at'): Builder
    {
        if (null !== $start && null !== $end) {
            return $query->whereBetween($field, [$start, $end]);
        }

        if (null !== $start) {
            return $query->where($field, '>=', $start);
        }

        if (null !== $end) {
            return $query->where($field, '<=', $end);
        }

        return $query;
    }
}
