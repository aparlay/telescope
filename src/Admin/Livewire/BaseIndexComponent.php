<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\QueryBuilder;
use Aparlay\Core\Admin\Livewire\Traits\CurrentUserTrait;
use Arr;
use Illuminate\Contracts\Database\Query\Builder;
use Livewire\Component;
use Livewire\WithPagination;

abstract class BaseIndexComponent extends Component
{
    use CurrentUserTrait;
    use WithPagination;
    public $currentUser;
    protected $paginationTheme = 'bootstrap';
    protected $listeners       = ['updateParent'];
    public int $perPage        = 15;
    public array $filter       = [];
    public array $sort         = [];
    protected $model;
    public string $dateFormat  = 'm/d/Y H:i:s';

    /**
     * @var Builder
     */
    protected $query;

    public function mount()
    {
        $this->currentUser = $this->currentUser();
    }

    public function updateParent()
    {
        $this->render();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function getDefaultSort(): array
    {
        return [];
    }

    public function updatingPerpage()
    {
        $this->resetPage();
    }

    abstract public function getAllowedSorts();

    abstract protected function getFilters();

    public function buildQuery(): Builder
    {
        $queryBuilder = (new QueryBuilder())
            ->for($this->model, $this->filter(), $this->sort)
            ->applyDefaultSort($this->getDefaultSort())
            ->applyFilters($this->getFilters())
            ->applySorts($this->getAllowedSorts());

        $query        = $queryBuilder->getQuery();
        $query->options(['allowDiskUse' => true]);

        return $query;
    }

    /**
     * @param mixed $order
     *
     * @return void
     */
    public function sort($field, $order = 1)
    {
        $value              = Arr::get($this->sort, $field);
        if ($value === -1) {
            $this->sort = [];

            return;
        }
        $newSort            = $order * ($value ?? -1) * -1;
        $this->sort         = [];
        $this->sort[$field] = $newSort;
    }

    public function index()
    {
        $query = $this->buildQuery();
        $this->appendQuery($query);

        return $query->paginate($this->perPage);
    }

    public function appendQuery($query)
    {
        // apply other queries when overriding
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    protected function filter(): array
    {
        return $this->filter;
    }
}
