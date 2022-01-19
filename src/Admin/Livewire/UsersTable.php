<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Models\Enums\UserVerificationStatus;
use App\Models\User;
use function view;

class UsersTable extends BaseIndexComponent
{
    public $model = User::class;

    public $selectedUser = null;

    protected $listeners = ['updateParent'];

    public function updateParent()
    {
        $this->render();
    }

    protected array $allowedFilters = [
        'email' => 'string',
        'gender' => 'int',
        'phone_number' => 'string',
        'status' => 'int',
        'text_search' => 'string',
        'verification_status' => 'int',
    ];

    public function buildQuery()
    {
        $query = parent::buildQuery();
        $query->with('userDocumentObjs');

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
