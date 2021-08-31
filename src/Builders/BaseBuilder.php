<?php

namespace Aparlay\Core\Builders;

use Aparlay\Core\Pagination\CoreCursorPaginator;
use Jenssegers\Mongodb\Eloquent\Builder;

class BaseBuilder extends Builder
{
    /**
     * Paginate the given query into a cursor paginator.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $cursorName
     * @param  \Illuminate\Pagination\Cursor|string|null  $cursor
     * @return \Illuminate\Contracts\Pagination\CursorPaginator
     */
    public function cursorPaginate($perPage = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null)
    {
        $cursor = CoreCursorPaginator::currentCursor();

        if ($cursor) {
            $apply = function ($query, $columns, $cursor) use (&$apply) {
                $query->where(function ($query) use ($columns, $cursor, $apply) {
                    $column = key($columns);
                    $direction = array_shift($columns);
                    $value = array_shift($cursor);

                    $query->where($column, $direction === 'asc' ? '>' : '<', $value);

                    if (! empty($columns)) {
                        $query->orWhere($column, $value);
                        $apply($query, $columns, $cursor);
                    }
                });
            };

            $apply($this, $columns, $cursor);
        }

        foreach ($columns as $column => $direction) {
            $this->orderBy($column, $direction);
        }

        $items = $this->limit($perPage + 1)->get();

        if ($items->count() <= $perPage) {
            return new CoreCursorPaginator($perPage);
        }

        $items->pop();

        return new CoreCursorPaginator($items, array_map(function ($column) use ($items) {
            return $items->last()->{$column};
        }, array_keys($columns)));
    }
}
