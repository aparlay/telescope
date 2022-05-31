<?php

namespace Aparlay\Core\Admin\Livewire\Traits;

use Auth;

trait CurrentUserTrait
{
    public function currentUser()
    {
        return Auth::guard('admin')->user();
    }
}
