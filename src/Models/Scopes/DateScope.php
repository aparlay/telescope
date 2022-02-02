<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\UTCDateTime;

trait DateScope
{
    public function scopeDate(Builder $query, UTCDateTime $start = null, UTCDateTime $end = null): Builder
    {
        if (null !== $start && null !== $end) {
            return $query->whereBetween('created_at', [$start, $end]);
        }

        if (null !== $start) {
            return $query->where('created_at', '$gte', $start);
        }

        if (null !== $end) {
            return $query->where('created_at', '$lte', $end);
        }

        return $query;
    }
}
