<?php

namespace Aparlay\Core\Models\Enums;

enum UserStatus: int implements Enum
{
    use EnumEnhancements;

    case PENDING = 0; // just registered without OTP confirmation
    case VERIFIED = 1; // OTP entered correctly
    case ACTIVE = 2; // username set
    case SUSPENDED = 3;
    case BLOCKED = 4;
    case DEACTIVATED = 10;

    public function label(): string
    {
        return match ($this) {
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
        return match ($this) {
            self::PENDING => 'warning',
            self::VERIFIED => 'info',
            self::ACTIVE => 'success',
            self::SUSPENDED => 'danger',
            self::BLOCKED, self::DEACTIVATED => 'indigo',
        };
    }
}
