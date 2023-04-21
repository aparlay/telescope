<?php

namespace Aparlay\Core\Models\Enums;

enum UserNotificationCategory: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::LIKES => __('likes'),
            self::COMMENTS => __('comments'),
            self::TIPS => __('tips'),
            self::SUBSCRIPTIONS => __('subscriptions'),
            self::FOLLOWS => __('follows'),
            self::UNREAD_MESSAGE => __('unread message'),
            self::SYSTEM => __('system'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::LIKES => 'info',
            self::COMMENTS => 'warning',
            self::TIPS => 'blue',
            self::SUBSCRIPTIONS => 'default',
            self::FOLLOWS => 'success',
            self::UNREAD_MESSAGE => 'black',
            self::SYSTEM => 'danger',
        };
    }

    case LIKES          = 1;
    case COMMENTS       = 2;
    case TIPS           = 3;
    case FOLLOWS        = 4;
    case SUBSCRIPTIONS  = 5;
    case UNREAD_MESSAGE = 6;
    case SYSTEM         = 10;
}
