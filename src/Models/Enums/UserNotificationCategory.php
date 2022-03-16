<?php

namespace Aparlay\Core\Models\Enums;

enum UserNotificationCategory: int implements Enum
{
    use EnumEnhancements;

    case LIKES = 1;
    case COMMENTS = 2;
    case TIPS = 3;
    case FOLLOWS = 4;
    case SYSTEM = 10;

    public function label(): string
    {
        return match ($this) {
            self::LIKES => __('likes'),
            self::COMMENTS => __('comments'),
            self::TIPS => __('tips'),
            self::FOLLOWS => __('follows'),
            self::SYSTEM => __('system'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::LIKES => 'info',
            self::COMMENTS => 'warning',
            self::TIPS => 'blue',
            self::FOLLOWS => 'success',
            self::SYSTEM => 'danger',
        };
    }
}
