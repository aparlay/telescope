<?php

namespace Aparlay\Core\Admin\Livewire;

use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\WithPagination;


abstract class BaseIndexComponent extends Component
{
    use WithPagination;

    public int $perPage = 10;
    protected $paginationTheme = 'bootstrap';
    public array $filter = [];
    protected $model;

    protected $query;

    /**
     * @var array
     * Allowed filters as key => type pairs for e.g
     * 'email' => 'string'
     */
    protected array $allowedFilters = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function buildQuery()
    {
        $query = $this->model::query();

        foreach ($this->prepareFilters()->all() as $fieldName => $fieldValue) {
            $query->where($fieldName, $fieldValue);
        }

        return $query;
    }

    public function index()
    {
        $query = $this->buildQuery();
        return $query->paginate($this->perPage);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function prepareFilters()
    {
        return collect($this->filter)
            ->filter(function ($value, $key) {
                $filterAllowed = Arr::get($this->allowedFilters, $key);
                return $value !== '' && $filterAllowed;
            })->map(function ($value, $key) {
                $castType = $this->allowedFilters[$key];
                settype($value, $castType);

                return $value;
            });
    }
}
