<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Jobs\DeleteUserConnect;
use Aparlay\Core\Jobs\DeleteUserMedia;
use Aparlay\Core\Jobs\UpdateAvatar;
use Aparlay\Core\Jobs\UpdateMedia;
use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\User;
use Exception;
use Illuminate\Support\Facades\Redis;

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
                UserGender::FEMALE->value => $femaleAvatar,
                UserGender::MALE->value => $maleAvatar,
                default => (random_int(0, 1)) ? $maleAvatar : $femaleAvatar,
            };

            $model->avatar = Cdn::avatar($filename);
        }

        parent::creating($model);
    }

    /**
     * Handle the User "creating" event.
     *
     * @param  User  $model
     * @return void
     * @throws Exception
     */
    public function saving($model): void
    {
        if (! empty($model->promo_link) && ! str_starts_with($model->promo_link, 'http')) {
            $model->promo_link = 'https://'.$model->promo_link;
        }

        // Reset the Redis cache
        if ($model->_id) {
            $cacheKey = 'SimpleUserCast:'.$model->_id;
            Redis::del($cacheKey);
            SimpleUserCast::cacheByUserId($model->_id);
        }

        parent::saving($model);
    }

    /**
     * Create a new event instance.
     *
     * @param  User  $model
     * @return void
     * @throws Exception
     */
    public function updated($model): void
    {
        if ($model->wasChanged('avatar')) {
            UpdateAvatar::dispatch((string) $model->_id)->onQueue('low');
        }

        if ($model->wasChanged('status')) {
            switch ($model->status) {
                case UserStatus::DEACTIVATED->value:
                case UserStatus::BLOCKED->value:
                    DeleteUserMedia::dispatch((string) $model->_id)->onQueue('low');
                    DeleteUserConnect::dispatch((string) $model->_id)->onQueue('low');
                    break;
            }
        }

        if ($model->wasChanged('visibility')) {
            UpdateMedia::dispatch((string) $model->_id, ['visibility' => $model->visibility])->onQueue('low');
        }
    }
}
