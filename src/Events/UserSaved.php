<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Jobs\DeleteMediaLike;
use Aparlay\Core\Jobs\DeleteUserConnect;
use Aparlay\Core\Jobs\DeleteUserMedia;
use Aparlay\Core\Jobs\UpdateAvatar;
use Aparlay\Core\Jobs\UpdateMedia;
use Aparlay\Core\Models\User;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSaved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(User $user)
    {
        if (! $user->wasRecentlyCreated && $user->wasChanged('avatar')) {
            dispatch((new UpdateAvatar((string) $user->_id))->onQueue('low'));
        }

        if (! $user->wasRecentlyCreated && $user->wasChanged('status')) {
            switch ($user->status) {
                case User::STATUS_DEACTIVATED:
                case User::STATUS_BLOCKED:
                    dispatch((new DeleteUserMedia((string) $user->_id))->onQueue('low'));
                    dispatch((new DeleteUserConnect((string) $user->_id))->onQueue('low'));
                    break;
            }
        }

        if (! $user->wasRecentlyCreated && $user->wasChanged('visibility')) {
            dispatch((new UpdateMedia((string) $user->_id, ['visibility' => $user->visibility]))->onQueue('low'));
        }
    }
}
