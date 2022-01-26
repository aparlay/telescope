<?php

namespace Aparlay\Core\Admin\Filters;

use Illuminate\Support\Arr;
use Jenssegers\Mongodb\Eloquent\Builder;

class QueryBuilder
{
    protected $query;
    protected $filter;
    protected $sort;

    private $allowedFilters;

    /**
     * @param $subject
     * @param $filter
     * @return $this
     */
    public function for($subject, $filter, $sort): self
    {
        $this->query = $subject::query();
        $this->filter = $filter;
        $this->sort = $sort;

        return $this;
    }

    private function prepareFilters($filters)
    {
        /** @var AbstractBaseFilter $filter */
        foreach ($filters as $filter) {
            $this->allowedFilters[$filter->getFieldName()] = $filter;
        }

        $preparedFilters = collect($this->filter)
            // filter empty string values and filters which are not presented in getFilters() array
            ->filter(function ($value, $key) {
                $filterAllowed = Arr::get($this->allowedFilters, $key);

                return $value !== '' && $filterAllowed;
            })->map(function ($value, $key) {
                /** @var AbstractBaseFilter $filter */
                $filter = $this->allowedFilters[$key];
                settype($value, $filter->getCastType());
                $filter->setFieldValue($value);

                return $value;
            });

        return $preparedFilters;
    }

    /**
     * @param $filters
     * @return $this
     * @throws \ErrorException
     */
    public function applyFilters($filters)
    {
        $preparedFilters = $this->prepareFilters($filters);

        \Log::info('prepared filters', [
            'ppp' => $preparedFilters,
        ]);

        /** @var AbstractBaseFilter $filter */
        foreach ($preparedFilters as $fieldName => $value) {
            $filter = $this->allowedFilters[$fieldName];
            if (! $filter) {
                throw new \ErrorException('This filter not allowed: '.$fieldName);
            }
            $filter($this->query);
        }

        return $this;
    }

    public function getSort()
    {
        $sortField = array_key_first($this->sort);

        if ($sortField) {
            $orders = [
                -1 => 'DESC', 1 => 'ASC',
            ];

            $direction = $orders[$this->sort[$sortField]];

            return collect([
                'column' => $sortField,
                'direction' => $direction,
            ]);
        }

        return false;
    }

    public function applySorts($allowedSorts)
    {
        foreach ($this->sort as $sortKey => $sortValue) {
            $sort = collect($allowedSorts)->search($sortKey);

            if ($sort === false) {
                throw new \ErrorException('This sort is not allowed: '.$sortKey);
            }
        }

        $sort = $this->getSort();

        if ($sort) {
            $this->query->orderBy($sort->get('column'), $sort->get('direction'));
        }

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
