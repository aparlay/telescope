<?php

namespace Aparlay\Core\Models\Queries;

use Illuminate\Support\Str;
use Jenssegers\Mongodb\Query\Builder as BaseBuilder;

class BaseQueryBuilder extends BaseBuilder
{
    /**
     * @param array $where
     * @return array
     */
    protected function compileWhereIn(array $where)
    {
        extract($where);

        // Convert id's.
        if ($column == '_id' || Str::endsWith($column, '._id')) {
            foreach ($values as &$value) {
                $value = $this->convertKey($value);
            }
        }

        return [$column => ['$in' => array_values($values)]];
    }

    /**
     * @param array $where
     * @return array
     */
    protected function compileWhereNotIn(array $where)
    {
        extract($where);

        // Convert id's.
        if ($column == '_id' || Str::endsWith($column, '._id')) {
            foreach ($values as &$value) {
                $value = $this->convertKey($value);
            }
        }

        return [$column => ['$nin' => array_values($values)]];
    }
}
