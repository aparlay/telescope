<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Scopes\UserScope;
use Aparlay\Core\Models\User as UserBase;
use OwenIt\Auditing\Contracts\Auditable;

class User extends UserBase implements Auditable
{
    use UserScope;
    use \OwenIt\Auditing\Auditable;

    public string $guard_name = 'admin';

    protected $hidden = ['password_hash', 'search'];

    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
        'password_hash',
    ];

    protected $fillable = [
        'username',
        'email',
        'email_verified',
        'bio',
        'features',
        'gender',
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
        'visibility' => 'integer',
        'verification_status' => 'integer',
    ];

    /**
     * {@inheritdoc}
     */
    public function generateTags(): array
    {
        return [
            $this->username,
            $this->_id,
        ];
    }

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
