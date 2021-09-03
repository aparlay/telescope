<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Jobs\DeleteUserConnect;
use Aparlay\Core\Jobs\DeleteUserMedia;
use Aparlay\Core\Jobs\UpdateAvatar;
use Aparlay\Core\Jobs\UpdateMedia;
use Aparlay\Core\Models\User;
use Exception;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param  User  $user
     * @return void
     * @throws Exception
     */
    public function creating(User $user)
    {
        if (empty($user->avatar)) {
            $maleAvatar = 'default_m_'.random_int(1, 120).'.png';
            $femaleAvatar = 'default_fm_'.random_int(1, 60).'.png';
            $filename = match ($user->gender) {
                User::GENDER_FEMALE => $femaleAvatar,
                User::GENDER_MALE => $maleAvatar,
                default => (random_int(0, 1)) ? $maleAvatar : $femaleAvatar,
            };

            $user->avatar = Cdn::avatar($filename);
        }
    }

    /**
     * Create a new event instance.
     *
     * @return void
     * @throws Exception
     */
    public function saved(User $user)
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
