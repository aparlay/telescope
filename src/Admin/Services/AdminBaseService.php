<?php

namespace Aparlay\Core\Admin\Services;

class AdminBaseService
{
    /**
     * @param string $field
     * @param $filterableField
     * @return bool
     */
    public function canFilterField(string $field, $filterableField): bool
    {
        return in_array($field, $filterableField) ? true : false;
    }

    /**
     * @param string $field
     * @param array $sorterableField
     * @return bool
     */
    public function canSortField(string $field, array $sorterableField): bool
    {
        return in_array($field, $sorterableField) ? true : false;
    }

    /**
     * @param array $filter
     * @param array $filterableField
     * @return array
     */
    public function cleanFilterFields(array $filter, array $filterableField): array
    {
        foreach ($filter as $key => $value) {
            if (! $this->canFilterField($key, $filterableField) || ! isset($value)) {
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
    public function cleanSortFields(array $sort, array $sorterableField): array
    {
        if (! isset($sort['field']) || ! $this->canSortField($sort['field'], $sorterableField)) {
            $sort = ['field' => 'created_at', 'by' => 'desc'];
        } elseif (! isset($sort['by'])) {
            $sort['by'] = 'desc';
        }

        return $sort;
    }
}
