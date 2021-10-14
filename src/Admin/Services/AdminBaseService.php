<?php

namespace Aparlay\Core\Admin\Services;

class AdminBaseService
{
    public $filterableField = [];
    public $sorterableField = [];
    public $sortDefault = ['created_at' => 'desc'];
    public $tableColumns = [];

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
        foreach ($sort as $field => $direction) {
            if (! $this->canSortField($field)) {
                $sort = $this->sortDefault;
            } elseif (! isset($sort[$field])) {
                $sort[$field] = 'desc';
            }
        }

        return $sort;
    }

    /**
     * @return array
     */
    public function fillTableColumns(): array
    {
        return $this->tableColumns = request()->columns ?? [];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        $columnArr = $this->fillTableColumns();
        $filterFields = [];
        foreach($columnArr as $column)
        {
            if($column['data']) {
                $filterFields[$column['data']] = $column['search']['value'];
            }
        }

        return $this->cleanFilterFields($filterFields);
    }

    /**
     * @param $color
     * @param $name
     * @return string
     */
    public function createBadge($color, $name): string
    {
        return '<span class="badge bg-'. $color .'">' . $name . '</span>';
    }

    /**
     * @param integer $numberColumn
     * @return array
     */
    public function getTableFieldByNumber(int $numberColumn): array
    {
        return $this->tableColumns[$numberColumn] ?? [];
    }

    /**
     * @return array
     */
    public function tableSort(): array
    {
        $sortTable = $this->sortDefault;
        $sortRequest = request()->order ?? [];

        if(isset($sortRequest[0])){
            if(isset($sortRequest[0]['column']) && isset($sortRequest[0]['dir'])){
                $tableField = $this->getTableFieldByNumber($sortRequest[0]['column']);
                if(!empty($tableField) && isset($tableField['data'])) {
                    $sortTable = [$tableField['data'] => $sortRequest[0]['dir']];
                }
            }
        }

        return $this->cleanSortFields($sortTable);
    }
}
