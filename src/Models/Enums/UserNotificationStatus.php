<?php

namespace Aparlay\Core\Models\Enums;

enum UserNotificationStatus: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::NOT_VISITED => __('not visited'),
            self::VISITED => __('visited'),
            self::INVISIBLE => __('invisible'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::NOT_VISITED => 'info',
            self::VISITED => 'warning',
            self::INVISIBLE => 'danger',
        };
    }

    case NOT_VISITED = -1;
    case VISITED     = 1;
    case INVISIBLE   = 2;
}
