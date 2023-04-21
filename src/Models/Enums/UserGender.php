<?php

namespace Aparlay\Core\Models\Enums;

enum UserGender: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::FEMALE => __('female'),
            self::MALE => __('male'),
            self::TRANSGENDER => __('transgender'),
            self::NOT_MENTION => __('not mention'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::FEMALE => 'info',
            self::MALE => 'success',
            self::TRANSGENDER => 'warning',
            self::NOT_MENTION => 'indigo',
        };
    }

    case FEMALE      = 0;
    case MALE        = 1;
    case TRANSGENDER = 2;
    case NOT_MENTION = 3;
}
