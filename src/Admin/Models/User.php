<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Admin\Models\Scopes\UserScope;
use Aparlay\Core\Models\User as UserBase;

class User extends UserBase
{
    use UserScope;

    public string $guard_name = 'admin';
    /**
     * @return string
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'secondary',
            self::STATUS_VERIFIED => 'success',
            self::STATUS_ACTIVE => 'primary',
            self::STATUS_SUSPENDED => 'warning',
            self::STATUS_BLOCKED => 'danger',
            self::STATUS_DEACTIVATED => 'danger',
        ];

        return $colors[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return $this->getStatuses()[$this->status];
    }

    /**
     * @return string
     */
    public function getGenderColorAttribute()
    {
        $colors = [
            self::GENDER_MALE => 'success',
            self::GENDER_FEMALE => 'primary',
            self::GENDER_NOT_MENTION => 'info',
            self::GENDER_TRANSGENDER => 'indigo',
        ];

        return $colors[$this->gender];
    }

    /**
     * @return string
     */
    public function getGenderNameAttribute()
    {
        return $this->getGenders()[$this->gender];
    }
}
