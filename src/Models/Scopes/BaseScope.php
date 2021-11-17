<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

trait BaseScope
{
    //TODO: scope too general, must refactor
    public function scopeFilter($query, $filters)
    {
        foreach ($filters as $key => $filter) {
            if (is_numeric($filter)) {
                $query->where($key, (int) $filter);
            } else {
                $query->where($key, 'regex', new Regex('^'.$filter));
            }
        }

        return $query;
    }

    public function scopeSortBy($query, $sorts): mixed
    {
        foreach ($sorts as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecentFirst($query): mixed
    {
        return $query->orderBy('created_at', 'desc');
    }

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
