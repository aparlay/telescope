<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Jobs\DeleteUserConnect;
use Aparlay\Core\Jobs\DeleteUserMedia;
use Aparlay\Core\Jobs\UpdateAvatar;
use Aparlay\Core\Jobs\UpdateMedia;
use Aparlay\Core\Models\User;
use Exception;

class UserObserver extends BaseModelObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param  User  $model
     * @return void
     * @throws Exception
     */
    public function creating($model): void
    {
        if (empty($model->avatar)) {
            $maleAvatar = 'default_m_'.random_int(1, 120).'.png';
            $femaleAvatar = 'default_fm_'.random_int(1, 60).'.png';
            $filename = match ($model->gender) {
                User::GENDER_FEMALE => $femaleAvatar,
                User::GENDER_MALE => $maleAvatar,
                default => (random_int(0, 1)) ? $maleAvatar : $femaleAvatar,
            };

            $model->avatar = Cdn::avatar($filename);
        }

        parent::creating($model);
    }

    /**
     * Create a new event instance.
     *
     * @param  User  $model
     * @return void
     * @throws Exception
     */
    public function updated($model)
    {
        if ($model->wasChanged('avatar')) {
            dispatch((new UpdateAvatar((string) $model->_id))->onQueue('low'));
        }

        if ($model->wasChanged('status')) {
            switch ($model->status) {
                case User::STATUS_DEACTIVATED:
                case User::STATUS_BLOCKED:
                    dispatch((new DeleteUserMedia((string) $model->_id))->onQueue('low'));
                    dispatch((new DeleteUserConnect((string) $model->_id))->onQueue('low'));
                    break;
            }
        }

        if ($model->wasChanged('visibility')) {
            dispatch((new UpdateMedia((string) $model->_id, ['visibility' => $model->visibility]))->onQueue('low'));
        }
    }
}
