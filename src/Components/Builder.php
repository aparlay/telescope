<?php

namespace Aparlay\Core\Components;

use Jenssegers\Mongodb\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    protected function ensureOrderForCursorPagination($shouldReverse = false)
    {
        if (empty($this->query->orders)) {
            $this->enforceOrderBy();
        }

        if ($shouldReverse) {
            $this->query->orders = collect($this->query->orders)->map(function ($direction) {
                return $direction === 1 ? -1 : 1;
            })->toArray();
        }

        return collect($this->query->orders)->map(function ($direction, $column) {
            return [
                'column' => $column,
                'direction' => $direction === 1 ? 'asc' : 'desc',
            ];
        })->values();
    }
}
