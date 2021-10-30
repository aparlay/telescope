<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Email;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

/**
 * Trait EmailScope
 * @package Aparlay\Core\Models\Scopes
 */
trait EmailScope
{
    /**
     * @param $query
     * @param $sorts
     *
     * @return mixed
     */
    public function scopeSortBy($query, $sorts): mixed
    {
        foreach ($sorts as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }

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
}
