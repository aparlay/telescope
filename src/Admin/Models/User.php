<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Scopes\UserScope;
use Aparlay\Core\Models\User as UserBase;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class User extends UserBase implements Auditable
{
    use UserScope;
    use \Aparlay\Core\Admin\Models\Auditable;
    use HasPushSubscriptions;

    public string $guard_name = 'admin';
    protected $hidden         = ['password_hash', 'search'];

    /**
     * Should the audit be strict?
     *
     * @var bool
     */
    protected $auditStrict = true;

    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude   = [
        'updated_by',
        'password_hash',
    ];
    protected $fillable       = [
        'username',
        'full_name',
        'email',
        'email_verified',
        'bio',
        'features',
        'gender',
        'birthday',
        'type',
        'status',
        'visibility',
        'referral_id',
        'promo_link',
        'verification_status',
        'country_alpha2',
        'payout_country_alpha2',
        'setting',
        'password_hash',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts          = [
        'type' => 'integer',
        'status' => 'integer',
        'gender' => 'integer',
        'visibility' => 'integer',
        'verification_status' => 'integer',
    ];

    public function generateTags(): array
    {
        return [
            $this->username,
            $this->_id,
        ];
    }

    public function getStatusColorAttribute(): string
    {
        return UserStatus::from($this->status)->badgeColor();
    }

    public function getStatusNameAttribute(): string
    {
        return UserStatus::from($this->status)->label();
    }

    public function getGenderColorAttribute(): string
    {
        return UserGender::from($this->gender)->badgeColor();
    }

    public function getGenderNameAttribute(): string
    {
        return UserGender::from($this->gender)->label();
    }

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

    public function getEmailTrimmedAttribute()
    {
        $atSignPosition = strpos($this->email, '@');

        return Str::limit(Str::substr($this->email, 0, $atSignPosition)).'@'.Str::substr($this->email, $atSignPosition);
    }
}
