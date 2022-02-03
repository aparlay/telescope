<?php

namespace Aparlay\Core\Admin\Livewire\Traits;

use Aparlay\Core\Admin\Models\User;

trait CurrentUserTrait
{
    public function currentUser()
    {
        $currentUserId = auth()->guard('admin')->id();
        $currentUser = User::find($currentUserId);

        return $currentUser;
    }
}
