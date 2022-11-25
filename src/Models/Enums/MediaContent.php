<?php

namespace Aparlay\Core\Models\Enums;

enum MediaContent: int implements Enum
{
    use EnumEnhancements;

    case FEMALE = 0;
    case MALE = 1;
    case TRANSGENDER = 2;

    public function label(): string
    {
        return match ($this) {
            self::FEMALE => __('female'),
            self::MALE => __('male'),
            self::TRANSGENDER => __('transgender'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::FEMALE => 'info',
            self::MALE => 'success',
            self::TRANSGENDER => 'warning',
        };
    }
}
