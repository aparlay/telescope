<?php

namespace Aparlay\Core\Models\Enums;

enum MediaSortCategories: string implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::DEFAULT => __('default'),
            self::GUEST => __('guest'),
            self::RETURNED => __('returned'),
            self::REGISTERED => __('registered'),
            self::PAID => __('paid'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DEFAULT => 'info',
            self::GUEST => 'danger',
            self::RETURNED => 'dark',
            self::REGISTERED => 'warning',
            self::PAID => 'success',
        };
    }

    case DEFAULT    = 'default';
    case GUEST      = 'guest';
    case RETURNED   = 'returned';
    case REGISTERED = 'registered';
    case PAID       = 'paid';
}
