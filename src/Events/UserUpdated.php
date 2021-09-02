<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Jobs\UploadAvatar;
use Aparlay\Core\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        dispatch((new UploadAvatar($user->id, $user->avatar))->onQueue('low'));
    }
}
