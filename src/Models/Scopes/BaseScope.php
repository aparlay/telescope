<?php

namespace Aparlay\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

trait BaseScope
{
    /**
     * @param $query
     * @param $filters
     * @return mixed
     */
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

    /**
     * @param $query
     * @param  UTCDateTime  $date
     * @return mixed
     */
    public function scopeSince($query, UTCDateTime $date): mixed
    {
        return $query->where('created_at', '>=', $date);
    }
}
