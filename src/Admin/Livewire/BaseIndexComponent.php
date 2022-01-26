<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\QueryBuilder;
use Jenssegers\Mongodb\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

abstract class BaseIndexComponent extends Component
{
    use WithPagination;

    public int $perPage = 10;
    protected $paginationTheme = 'bootstrap';
    public array $filter = [];
    public array $sort = [];

    protected $model;

    /**
     * @var Builder
     */
    protected $query;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    abstract public function getAllowedSorts();

    abstract protected function getFilters();

    public function buildQuery(): Builder
    {
        $queryBuilder = (new QueryBuilder())
            ->for($this->model, $this->filter, $this->sort)
            ->applyFilters($this->getFilters())
            ->applySorts($this->getAllowedSorts());

        $query = $queryBuilder->getQuery();
        $query->options(['allowDiskUse' => true]);

        return $query;
    }

    /**
     * @param $field
     * @return void
     */
    public function sort($field)
    {
        $value = \Arr::get($this->sort, $field);
        if ($value === -1) {
            $this->sort = [];

            return;
        }
        $newSort = ($value ?? -1) * -1;
        $this->sort = [];
        $this->sort[$field] = $newSort;
    }

    public function index()
    {
        $query = $this->buildQuery();

        return $query->paginate($this->perPage);
    }
}
