<?php

namespace Aparlay\Core\Models\Enums;

enum AlertStatus: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::NOT_VISITED => __('not visited'),
            self::VISITED => __('visited'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::NOT_VISITED => 'info',
            self::VISITED => 'warning',
        };
    }

    case NOT_VISITED = -1;
    case VISITED     = 1;
}
