<?php

namespace Aparlay\Core\Admin\Services;

class AdminBaseService
{
    public $filterableField = [];
    public $sorterableField = [];
    public $sortDefault = ['created_by'=>'desc'];
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
                $sort = ['created_at', 'desc'];
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
                if(!empty($tableField) && isset($tableField['name'])) {
                    $sortTable = [$tableField['name'] => $sortRequest[0]['dir']];
                }
            }
        }
        return $this->cleanSortFields($sortTable);
    }

    /**
     * @param collection $mediaList
     * @return array
     */
    public function buildData($collectionList): array
    {
        $data = [];
        foreach($collectionList as $collect){
            $dataArr = [];
            foreach($this->tableColumns as $keyColumn => $column){
                if(empty($column['name'])) continue;
                if($column['name'] == 'status') {
                    $dataArr['status'] = $this->getColoredStatus($collect);
                }else{
                    $dataArr[$column['name']] = $collect[$column['name']] ?? '';
                }
            }
            if(!empty($dataArr)) {
                $dataArr['id'] = $collect['id'];
                $data[] = $dataArr;
            }
        }

        return [
            'draw' => $collectionList->perPage() ?? config('core.admin.lists.page_count'),
            'recordsTotal' => $collectionList->total() ?? 0,
            'recordsFiltered' => $collectionList->total() ?? 0,
            'data' => $data
        ];
    }

    public function getColoredStatus($collectStatus)
    {
        if(is_array($collectStatus->status)){
            $statuses = [];
            foreach($collectStatus->status as $status){
                $statuses[] = $collectStatus->status_color;
            }
            return $statuses;
        }else{
            return $collectStatus->status_color;
        }
    }
}
