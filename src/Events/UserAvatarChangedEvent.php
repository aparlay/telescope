<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\User;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAvatarChangedEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public User $user, public string $avatar)
    {
    }
}
