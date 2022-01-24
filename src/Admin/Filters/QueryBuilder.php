<?php

namespace Aparlay\Core\Admin\Filters;

use Illuminate\Support\Arr;
use Jenssegers\Mongodb\Eloquent\Builder;

class QueryBuilder
{
    protected $query;
    protected $filter;
    private $allowedFilters;

    /**
     * @param $subject
     * @param $filter
     * @return $this
     */
    public function for($subject, $filter): self
    {
        $this->query = $subject::query();
        $this->filter = $filter;

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
     * @return Builder
     * @throws \ErrorException
     */
    public function applyFilters($filters)
    {
        $preparedFilters = $this->prepareFilters($filters);

        /** @var AbstractBaseFilter $filter */
        foreach ($preparedFilters as $fieldName => $value) {
            $filter = $this->allowedFilters[$fieldName];
            if (! $filter) {
                throw new \ErrorException('This filter not allowed: '.$fieldName);
            }
            $filter($this->query);
        }

        return $this->query;
    }
}
