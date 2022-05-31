<?php

namespace Aparlay\Core\Admin\Livewire\Traits;

use Aparlay\Core\Admin\Models\User;
use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait CurrentUserTrait
{

    use AuthorizesRequests;

    public function currentUser()
    {
        //$currentUserId = Auth::guard('admin')->user()->id();

        return Auth::guard('admin')->user();
    }
}
