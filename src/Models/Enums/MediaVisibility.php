<?php

namespace Aparlay\Core\Models\Enums;

enum MediaVisibility: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::PRIVATE => __('private'),
            self::PUBLIC => __('public'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PRIVATE => 'warning',
            self::PUBLIC => 'success',
        };
    }

    case PRIVATE = 0;
    case PUBLIC  = 1;
}
