<?php

namespace Aparlay\Core\Models\Enums;

enum UserWsState: string implements Enum
{
    use EnumEnhancements;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('active'),
            self::INACTIVE => __('inactive'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'warning',
        };
    }
}