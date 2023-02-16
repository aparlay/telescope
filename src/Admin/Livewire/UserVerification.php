<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Livewire\Modals\UserVerificationModal;
use Aparlay\Core\Admin\Livewire\Traits\CurrentUserTrait;

class UserVerification extends UserVerificationModal
{
    use CurrentUserTrait;
    public $user;
    public $documents = [];
    public $documentsData;

    public function render()
    {
        return view(
            'default_view::admin.pages.user.tabs.edit.user-verification',
        );
    }
}
