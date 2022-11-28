<?php

namespace Aparlay\Core\Models\Enums;

enum UserSettingShowAdultContent: int implements Enum
{
    use EnumEnhancements;

    case NEVER = 1;
    case ASK = 2;
    case TOPLESS = 3;
    case ALL = 4;

    public function label(): string
    {
        return match ($this) {
            self::NEVER => __('never'),
            self::ASK => __('ask'),
            self::TOPLESS => __('topless'),
            self::ALL => __('all'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::NEVER => 'default',
            self::ASK => 'info',
            self::TOPLESS => 'warning',
            self::ALL => 'success',
        };
    }
}
