<?php

namespace Aparlay\Core\Models\Queries;

use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;

class EloquentQueryBuilder extends Builder
{
    public function filter(array $filters): self
    {
        foreach ($filters as $key => $filter) {
            if (is_numeric($filter)) {
                $this->where($key, (int) $filter);
            } else {
                $this->where($key, 'regex', new Regex('^' . $filter));
            }
        }

        return $this;
    }

    public function sortBy(array $sorts): self
    {
        foreach ($sorts as $field => $direction) {
            $this->orderBy($field, $direction);
        }

        return $this;
    }

    public function recentFirst(): self
    {
        return $this->orderBy('created_at', 'desc');
    }

    public function recent(): self
    {
        return $this->recentFirst();
    }

    public function since(UTCDateTime $date): self
    {
        return $this->where('created_at', '>=', $date);
    }

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

    /**
     * @return $this
     */
    public function whereId(ObjectId|string $id, string $field = '_id'): self
    {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);

        return $this->where($field, $id);
    }

    /**
     * @return $this
     */
    public function whereIdNeq(ObjectId|string $id, string $field = '_id'): self
    {
        $id = $id instanceof ObjectId ? $id : new ObjectId($id);

        return $this->where($field, '!=', $id);
    }

    public function whereInIds(string $field, array $ids): self
    {
        $castedIds = [];
        foreach ($ids as $id) {
            $castedIds[] = $id instanceof ObjectId ? $id : new ObjectId($id);
        }

        return $this->whereIn($field, $castedIds);
    }

    /**
     * @return $this
     */
    public function date(?UTCDateTime $start = null, ?UTCDateTime $end = null, string $field = 'created_at'): self
    {
        if (null !== $start && null !== $end) {
            return $this->whereBetween($field, [$start, $end]);
        }

        if (null !== $start) {
            return $this->where($field, '>=', $start);
        }

        if (null !== $end) {
            return $this->where($field, '<=', $end);
        }

        return $this;
    }
}
