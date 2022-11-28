<?php

namespace Aparlay\Core\Models\Enums;

enum UserSettingShowAdultContent: int implements Enum
{
    use EnumEnhancements;

    case NO = 1;
    case ASK = 2;
    case ALWAYS = 3;

    public function label(): string
    {
        return match ($this) {
            self::NO => __('no'),
            self::ASK => __('ask'),
            self::ALWAYS => __('yes'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::NO => 'warning',
            self::ASK => 'info',
            self::ALWAYS => 'success',
        };
    }
}
