<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use App\Models\User;
use Jenssegers\Mongodb\Eloquent\Builder;

class UsersTable extends BaseIndexComponent
{
    public $model = User::class;

    public $selectedUser;

    protected $listeners = ['updateParent'];

    public function updateParent()
    {
        $this->render();
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        $verificationStatusFilter = new FilterExact('verification_status', 'int');
        $verificationStatusFilter->setDefaultValue(UserVerificationStatus::PENDING->value);

        return [
            new FilterPartial('username', 'string'),
            new FilterExact('gender', 'int'),
            new FilterScope('email', 'string', 'email'),
            new FilterScope('country', 'string', 'countryAlpha2'),
            new FilterExact('status', 'int'),
            new FilterScope('text_search', 'string', 'textSearch'),
            $verificationStatusFilter,
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();

        $query->orderByDesc('created_at');
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
