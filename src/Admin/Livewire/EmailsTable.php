<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use Aparlay\Core\Admin\Models\Email;
use Jenssegers\Mongodb\Eloquent\Builder;

class EmailsTable extends BaseIndexComponent
{
    public $model = Email::class;
    protected $listeners = ['updateParent'];

    public function updateParent()
    {
        $this->render();
    }

    public function getAllowedSorts()
    {
        return [
            'username',
            'email',
            'type',
            'status',
            'created_at',
        ];
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('username', 'string', 'user.username'),
            new FilterExact('type', 'int'),
            new FilterExact('status', 'int'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();
        $query->with('userObj');

        return $query;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.emails-table', [
           'models' => $this->index(),
        ]);
    }
}
