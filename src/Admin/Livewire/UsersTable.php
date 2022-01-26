<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use App\Models\User;
use Jenssegers\Mongodb\Eloquent\Builder;

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
            'phone',
            'full_name',
            'status',
            'visibility',
            'media_count',
            'likes_count',
            'follower_count',
            'created_at',
            'email_verified',
        ];
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('full_name', 'string'),
            new FilterPartial('username', 'string'),
            new FilterExact('gender', 'int'),
            new FilterScope('email', 'string', 'email'),
            new FilterExact('status', 'int'),
            new FilterScope('text_search', 'string', 'textSearch'),
            (new FilterExact('verification_status', 'int')),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();

        $query->options(['allowDiskUse' => true]);

        return $query;
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
