<?php

namespace Aparlay\Core\Admin\Livewire\Traits;

use Aparlay\Core\Admin\Models\User;
use Auth;

trait CurrentUserTrait
{
    public function currentUser()
    {
        //$currentUserId = Auth::guard('admin')->user()->id();

        return Auth::guard('admin')->user();
    }
}
