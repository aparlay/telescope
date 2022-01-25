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
    protected $model;

    /**
     * @var Builder
     */
    protected $query;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function updatingPerpage()
    {
        $this->resetPage();
    }

    protected function getFilters()
    {
        return [];
    }

    public function buildQuery(): Builder
    {
        return (new QueryBuilder())
            ->for($this->model, $this->filter)
            ->applyFilters($this->getFilters());
    }

    public function index()
    {
        $query = $this->buildQuery();

        return $query->paginate($this->perPage);
    }
}
