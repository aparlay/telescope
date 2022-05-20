<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Scopes\UserScope;
use Aparlay\Core\Models\User as UserBase;

class User extends UserBase
{
    use UserScope;

    public string $guard_name = 'admin';

    protected $hidden = ['password_hash'];

    protected $fillable = [
        'username',
        'email',
        'email_verified',
        'bio',
        'features',
        'gender',
        'interested_in',
        'type',
        'status',
        'visibility',
        'referral_id',
        'promo_link',
        'verification_status',
        'country_alpha2',
        'payout_country_alpha2',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'integer',
        'status' => 'integer',
        'gender' => 'integer',
        'interested_in' => 'integer',
        'visibility' => 'integer',
        'verification_status' => 'integer',
    ];

    /**
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return UserStatus::from($this->status)->badgeColor();
    }

    /**
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return UserStatus::from($this->status)->label();
    }

    /**
     * @return string
     */
    public function getGenderColorAttribute(): string
    {
        return UserGender::from($this->gender)->badgeColor();
    }

    /**
     * @return string
     */
    public function getGenderNameAttribute(): string
    {
        return UserGender::from($this->gender)->label();
    }

    /**
     * @return string
     */
    public function getVerificationStatusNameAttribute(): string
    {
        return UserVerificationStatus::from($this->verification_status)->label();
    }

    public function adminlte_image()
    {
        return 'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        return 'That\'s a nice guy';
    }

    public function adminlte_profile_url()
    {
        return 'profile/username';
    }
}
