<?php

namespace Aparlay\Core\Admin\Services;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class AdminBaseService
{
    public $filterableField = [];
    public $sorterableField = [];
    public $sortDefault     = ['created_at' => 'desc'];
    public $tableColumns    = [];

    public function canFilterField(string $field): bool
    {
        return in_array($field, $this->filterableField);
    }

    public function canSortField(string $field): bool
    {
        return in_array($field, $this->sorterableField);
    }

    public function cleanFilterFields(array $filter): array
    {
        foreach ($filter as $key => $value) {
            if (!$this->canFilterField($key) || !isset($value)) {
                unset($filter[$key]);
            } elseif (is_numeric($value)) {
                $filter[$key] = intval($value);
            } elseif (mb_strlen($value) < 3) {
                unset($filter[$key]);
            }
        }

        return $filter;
    }

    public function cleanSortFields(array $sort): array
    {
        foreach ($sort as $field => $direction) {
            if (!$this->canSortField($field)) {
                $sort = $this->sortDefault;
            } elseif (!isset($sort[$field])) {
                $sort[$field] = 'desc';
            }
        }

        return $sort;
    }

    public function fillTableColumns(): array
    {
        return $this->tableColumns = request()->columns ?? [];
    }

    public function getFilters(): array
    {
        $columnArr    = $this->fillTableColumns();
        $filterFields = [];
        foreach ($columnArr as $column) {
            if ($column['data']) {
                $filterFields[$column['data']] = $column['search']['value'];
            }
        }

        return $this->cleanFilterFields($filterFields);
    }

    public function getTableFieldByNumber(int $numberColumn): array
    {
        return $this->tableColumns[$numberColumn] ?? [];
    }

    public function tableSort(): array
    {
        $sortTable   = $this->sortDefault;
        $sortRequest = request()->order ?? [];

        if (isset($sortRequest[0])) {
            if (isset($sortRequest[0]['column'], $sortRequest[0]['dir'])) {
                $tableField = $this->getTableFieldByNumber($sortRequest[0]['column']);
                if (!empty($tableField) && isset($tableField['data'])) {
                    $sortTable = [$tableField['data'] => $sortRequest[0]['dir']];
                }
            }
        }

        return $this->cleanSortFields($sortTable);
    }

    public function getDateRangeFilter($dates): array
    {
        $dateRangeArr = explode(' - ', $dates);

        return [
            'start' => new UTCDateTime(Carbon::parse($dateRangeArr[0])->startOfDay()),
            'end' => new UTCDateTime(Carbon::parse($dateRangeArr[1])->endOfDay()),
        ];
    }
}
