<?php

namespace Aparlay\Core\Models\Enums;

enum UserStatus: int implements Enum
{
    case PENDING = 0;
    case VERIFIED = 1;
    case ACTIVE = 2;
    case SUSPENDED = 3;
    case BLOCKED = 4;
    case DEACTIVATED = 10;

    public function label(): string
    {
        return match($this)
        {
            self::PENDING => __('pending'),
            self::VERIFIED => __('verified'),
            self::ACTIVE => __('active'),
            self::SUSPENDED => __('suspended'),
            self::BLOCKED => __('blocked'),
            self::DEACTIVATED => __('deactivated'),
        };
    }

    public function badgeColor(): string
    {
        return match($this)
        {
            self::PENDING => 'warning',
            self::VERIFIED => 'info',
            self::ACTIVE => 'success',
            self::SUSPENDED => 'danger',
            self::BLOCKED => 'indigo',
            self::DEACTIVATED => 'indigo',
        };
    }
}
