<?php

namespace Aparlay\Core\Admin\Models\Scopes;

use MongoDB\BSON\Regex;

trait MediaScope
{
    public function scopeSortBy($query, $sort)
    {
        $key = array_keys($sort)[0];

        return $query->orderBy($key, $sort[$key]);
    }

    public function scopeFilter($query, $filters)
    {
        foreach ($filters as $key => $filter) {
            if (is_numeric($filter)) {
                $query->where($key, '=', (int) $filter);
            } else {
                $query->where($key, 'regex', new Regex('^'.$filter));
            }
        }

        return $query;
    }
}
