<?php

namespace Aparlay\Core\Admin\Models\Scopes;

use MongoDB\BSON\Regex;

trait UserScope
{
    public function scopeDesc($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeFilter($query, $filters)
    {
        foreach($filters as $key => $filter)
        {
            if(is_numeric($filter)) {
                $query->where($key, $filter);
            }else {
                $query->where($key, 'regex', new Regex('^' . $filter));
            }
        }

        return $query;
    }
}
