<?php

namespace Aparlay\Core\Models\Enums;

enum AlertStatus: int implements Enum
{
    use EnumEnhancements;

    case NOT_VISITED = 0;
    case VISITED = 1;

    public function label(): string
    {
        return match ($this) {
            self::NOT_VISITED => __('queued'),
            self::VISITED => __('uploaded'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::NOT_VISITED => 'info',
            self::VISITED => 'warning',
        };
    }
}
