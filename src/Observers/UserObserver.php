<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Casts\SimpleUserCast;
use Aparlay\Core\Events\AvatarChangedEvent;
use Aparlay\Core\Events\UsernameChangedEvent;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\IP;
use Aparlay\Core\Jobs\DeleteUserConnect;
use Aparlay\Core\Jobs\DeleteUserMediaComments;
use Aparlay\Core\Jobs\DeleteUserMediaLikes;
use Aparlay\Core\Jobs\UpdateMedia;
use Aparlay\Core\Jobs\UpdateUserCountry;
use Aparlay\Core\Jobs\UpdateUserMediaStatus;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserShowOnlineStatus;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
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
        if (empty($model->status)) {
            $model->status = UserStatus::PENDING->value;
        }
        if (! in_array($model->gender, array_keys(User::getGenders()), true)) {
            $model->gender = UserGender::MALE->value;
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
        if ($model->_id && $model->isDirty(['username', 'avatar', 'is_verified'])) {
            SimpleUserCast::cacheByUserId($model->_id, true);
        }

        if ($model->isDirty(['username', 'email', 'phone_number', 'full_name'])) {
            $text_search = explode(' ', $model->full_name);
            $text_search[] = $model->username;
            $text_search[] = $model->email;
            $text_search[] = $model->phone_number;
            $model->text_search = array_values(array_filter(array_map('strtolower', $text_search)));
        }

        if ($model->isDirty(['country_alpha2'])) {
            $setting = $model->setting;
            $setting['payment']['allow_unverified_cc'] = ! $model->is_tier3;
            $setting['payment']['block_unverified_cc'] = $model->is_tier3;
            $setting['payment']['block_cc_payments'] = false;
            $model->setting = $setting;
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
    public function updated(User $model): void
    {
        if ($model->wasChanged('username')) {
            UsernameChangedEvent::dispatch($model);
        }
        if ($model->wasChanged('avatar')) {
            AvatarChangedEvent::dispatch($model);
        }

        if ($model->wasChanged('status') && $model->status != $model->getOriginal('status')) {
            switch ($model->status) {
                case UserStatus::DEACTIVATED->value:
                    UpdateUserMediaStatus::dispatch((string) $model->_id, MediaStatus::USER_DELETED->value);
                    DeleteUserConnect::dispatch((string) $model->_id);
                    DeleteUserMediaComments::dispatch((string) $model->_id);
                    DeleteUserMediaLikes::dispatch((string) $model->id);
                    break;
                case UserStatus::BLOCKED->value:
                    $model->unsearchable();
                    UpdateUserMediaStatus::dispatch((string) $model->_id, MediaStatus::ADMIN_DELETED->value);
                    DeleteUserConnect::dispatch((string) $model->_id);
                    DeleteUserMediaComments::dispatch((string) $model->_id);
                    DeleteUserMediaLikes::dispatch((string) $model->id);
                    break;
                case UserStatus::ACTIVE->value:
                    if ($model->getOriginal('status') == UserStatus::SUSPENDED->value) {
                        UpdateUserMediaStatus::dispatch((string) $model->_id, MediaStatus::DENIED->value);
                    }
                    break;
            }
        }

        if ($model->wasChanged('visibility')) {
            UpdateMedia::dispatch((string) $model->_id, [
                'visibility' => $model->is_public ? MediaVisibility::PUBLIC->value : MediaVisibility::PRIVATE->value,
            ]);
        }
    }

    public function saved($model)
    {
        if (empty($model->country_alpha2) && ! empty(IP::trueAddress()) && IP::trueAddress() !== '127.0.0.1') {
            UpdateUserCountry::dispatch((string) $model->_id, IP::trueAddress());
        }
    }
}
