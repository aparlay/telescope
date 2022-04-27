<?php

namespace Aparlay\Core\Models\Enums;

enum UserShowOnlineStatus: int implements Enum
{
    use EnumEnhancements;

    case NONE = 0;
    case FOLLOWERS = 1;
    case All = 2;

    public function label(): string
    {
        return match ($this) {
            self::NONE => __('pending'),
            self::FOLLOWERS => __('verified'),
            self::All => __('active'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::NONE => 'warning',
            self::FOLLOWERS => 'info',
            self::All => 'success',
        };
    }
}
