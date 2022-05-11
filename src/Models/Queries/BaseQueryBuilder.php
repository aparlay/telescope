<?php

namespace Aparlay\Core\Models\Queries;

use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

class BaseQueryBuilder extends Builder
{
    /**
     * @param $filters
     * @return $this
     */
    public function filter($filters): self
    {
        foreach ($filters as $key => $filter) {
            if (is_numeric($filter)) {
                $this->where($key, (int) $filter);
            } else {
                $this->where($key, 'regex', new Regex('^'.$filter));
            }
        }

        return $this;
    }

    /**
     * @param $sorts
     * @return $this
     */
    public function sortBy($sorts): self
    {
        foreach ($sorts as $field => $direction) {
            $this->orderBy($field, $direction);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function recentFirst(): self
    {
        return $this->orderBy('created_at', 'desc');
    }

    /**
     * @param  UTCDateTime  $date
     * @return $this
     */
    public function since(UTCDateTime $date): self
    {
        return $this->where('created_at', ['$gte' => $date]);
    }

    /**
     * @inheritdoc
     */
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
