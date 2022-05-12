<?php

namespace Aparlay\Core\Models\Queries;

use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

class BaseQueryBuilder extends Builder
{
    /**
     * @param array $filters
     * @return self
     */
    public function filter(array $filters): self
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
     * @param array $sorts
     * @return self
     */
    public function sortBy(array $sorts): self
    {
        foreach ($sorts as $field => $direction) {
            $this->orderBy($field, $direction);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function recentFirst(): self
    {
        return $this->orderBy('created_at', 'desc');
    }

    /**
     * @return self
     */
    public function recent(): self
    {
        return $this->recentFirst();
    }

    /**
     * @param  UTCDateTime  $date
     * @return self
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
