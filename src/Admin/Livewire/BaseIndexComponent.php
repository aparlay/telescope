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

    public abstract function getAllowedSorts();

    protected function getFilters()
    {
        return [];
    }

    public function buildQuery(): Builder
    {
        $queryBuilder = (new QueryBuilder())
            ->for($this->model, $this->filter, $this->sort)
            ->applyFilters($this->getFilters())
            ->applySorts($this->getAllowedSorts());


        return $queryBuilder->getQuery();
    }


    public function sort($field)
    {
        $value = $this->sort[$field] ?? 1;
        $newSort = $value * -1;
        $this->sort = [];
        $this->sort[$field] = $newSort;
    }


    public function index()
    {
        $query = $this->buildQuery();

        return $query->paginate($this->perPage);
    }
}
