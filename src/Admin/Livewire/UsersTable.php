<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use Aparlay\Core\Admin\Models\User;

class UsersTable extends BaseIndexComponent
{
    public $model = User::class;
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
            'full_name',
            'status',
            'visibility',
            'country',
            'stats.counters.medias',
            'stats.counters.likes',
            'stats.counters.followrs',
            'created_at',
            'email_verified',
        ];
    }

    protected function getFilters()
    {
        return [
            new FilterExact('email_verified', 'boolean'),
            new FilterPartial('username', 'string'),
            new FilterPartial('email', 'string'),
            new FilterPartial('full_name', 'string'),
            new FilterScope('country', 'string', 'countryAlpha2'),
            new FilterExact('gender', 'int'),
            new FilterExact('status', 'int'),
            new FilterScope('text_search', 'string', 'textSearch'),
            new FilterExact('verification_status', 'int'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.users-table', [
           'users' => $this->index(),
        ]);
    }
}
