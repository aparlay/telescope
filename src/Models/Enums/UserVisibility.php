<?php

namespace Aparlay\Core\Models\Enums;

enum UserVisibility: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::PRIVATE => __('private'),
            self::PUBLIC => __('public'),
            self::INVISIBLE_BY_ADMIN => __('invisible'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PRIVATE => 'warning',
            self::PUBLIC => 'success',
            self::INVISIBLE_BY_ADMIN => 'danger',
        };
    }

    case PRIVATE            = 0;
    case PUBLIC             = 1;
    case INVISIBLE_BY_ADMIN = 2;
}
