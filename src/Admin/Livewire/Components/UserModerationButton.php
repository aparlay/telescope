<?php

namespace Aparlay\Core\Admin\Livewire\Components;

use Aparlay\Core\Admin\Models\User;
use Livewire\Component;

class UserModerationButton extends Component
{
    public $user;

    public function updateParent()
    {
        $this->render();
    }


    public function mount($userId)
    {
        $this->user = User::find($userId);
    }



    public function render()
    {
        return view('default_view::livewire.components.user-moderation-button');
    }
}
