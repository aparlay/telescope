<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Jenssegers\Mongodb\Eloquent\Builder;

class UsersModerationTable extends BaseIndexComponent
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
            'country',
            'gender',
            'status',
            'verification_status',
            'created_at',
        ];
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('username', 'string'),
            new FilterExact('gender', 'int'),
            new FilterExact('email_verified', 'int'),
            new FilterScope('email', 'string', 'email'),
            new FilterScope('country', 'string', 'countryAlpha2'),
            new FilterExact('status', 'int'),
            new FilterScope('text_search', 'string', 'textSearch'),
            (new FilterExact('verification_status', 'int')),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();
        $query->whereIn(
            'verification_status',
            [UserVerificationStatus::PENDING->value, UserVerificationStatus::UNDER_REVIEW->value]
        );

        return $query;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.users-moderation-table', [
           'users' => $this->index(),
        ]);
    }
}
