<?php

namespace Aparlay\Core\Models\Enums;

enum UserType: int implements Enum
{
    use EnumEnhancements;

    case USER = 0;
    case ADMIN = 1;

    public function label(): string
    {
        return match ($this) {
            self::USER => __('user'),
            self::ADMIN => __('admin'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::USER => 'warning',
            self::ADMIN => 'success',
        };
    }
}
