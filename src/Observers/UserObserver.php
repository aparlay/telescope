<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Api\V1\Notifications\UserDeactivateAccount;
use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\IP;
use Aparlay\Core\Jobs\DeleteUserConnect;
use Aparlay\Core\Jobs\DeleteUserMedia;
use Aparlay\Core\Jobs\UpdateAvatar;
use Aparlay\Core\Jobs\UpdateMedia;
use Aparlay\Core\Jobs\UpdateUserCountry;
use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Aparlay\Core\Models\Enums\UserShowOnlineStatus;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
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
        if (empty($model->status)) {
            $model->status = UserStatus::PENDING->value;
        }
        if (empty($model->gender)) {
            $model->gender = UserGender::MALE->value;
        }
        if (empty($model->interested_in)) {
            $model->interested_in = UserInterestedIn::FEMALE->value;
        }
        if (empty($model->visibility)) {
            $model->visibility = UserVisibility::PUBLIC->value;
        }
        if (empty($model->show_online_status)) {
            $model->show_online_status = UserShowOnlineStatus::All->value;
        }
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

        if (empty($model->verification_status)) {
            $model->verification_status = UserVerificationStatus::UNVERIFIED->value;
        }

        parent::creating($model);
    }

    public function created($model)
    {
        if (empty($model->country_alpha2)) {
            UpdateUserCountry::dispatch((string) $model->_id, IP::trueAddress());
        }
    }

    /**
     * Handle the User "creating" event.
     *
     * @param  User  $model
     * @return void
     * @throws Exception|\Psr\SimpleCache\InvalidArgumentException
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

        if ($model->isDirty(['username', 'email', 'phone_number', 'full_name'])) {
            $text_search = explode(' ', $model->full_name);
            $text_search[] = $model->username;
            $text_search[] = $model->email;
            $text_search[] = $model->phone_number;
            $model->text_search = $text_search;
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
            UpdateAvatar::dispatch((string) $model->_id);
        }

        if ($model->wasChanged('status')) {
            switch ($model->status) {
                case UserStatus::DEACTIVATED->value:
                case UserStatus::BLOCKED->value:
                    DeleteUserMedia::dispatch((string) $model->_id);
                    DeleteUserConnect::dispatch((string) $model->_id);
                    break;
            }
            if ($model->status === UserStatus::DEACTIVATED->value) {
                $model->notify(new UserDeactivateAccount());
            }
        }

        if ($model->wasChanged('visibility')) {
            UpdateMedia::dispatch((string) $model->_id, ['visibility' => $model->visibility]);
        }
    }
}
