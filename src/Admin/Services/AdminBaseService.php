<?php

namespace Aparlay\Core\Admin\Services;

class AdminBaseService
{
    public $filterableField = [];
    public $sorterableField = [];
    /**
     * @param string $field
     * @param $filterableField
     * @return bool
     */
    public function canFilterField(string $field): bool
    {
        return in_array($field, $this->filterableField) ? true : false;
    }

    /**
     * @param string $field
     * @param array $sorterableField
     * @return bool
     */
    public function canSortField(string $field): bool
    {
        return in_array($field, $this->sorterableField) ? true : false;
    }

    /**
     * @param array $filter
     * @param array $filterableField
     * @return array
     */
    public function cleanFilterFields(array $filter): array
    {
        foreach ($filter as $key => $value) {
            if (! $this->canFilterField($key) || ! isset($value)) {
                unset($filter[$key]);
            } elseif (is_numeric($value)) {
                $filter[$key] = intval($value);
            } elseif (mb_strlen($value) < 3) {
                unset($filter[$key]);
            }
        }

        return $filter;
    }

    /**
     * @param array $sort
     * @param array $sorterableField
     * @return array
     */
    public function cleanSortFields(array $sort): array
    {
        foreach($sort as $field => $direction) {
            if(!$this->canSortField($field)) {
                $sort = ['created_at', 'desc'];
            } elseif (! isset($sort[$field])) {
                $sort[$field] = 'desc';
            }
        }

        return $sort;
    }
}
