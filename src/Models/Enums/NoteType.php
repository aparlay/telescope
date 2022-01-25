<?php

namespace Aparlay\Core\Models\Enums;

enum NoteType: string implements Enum
{
    case SUSPEND = 'suspend';
    case UNSUSPEND = 'unsuspend';
    case BAN = 'ban';
    case UNBAN = 'unban';
    case WARNING_MESSAGE = 'warning message';
    case BAN_ALL_CC_PAYMENT = 'ban all cc payments';
    case UNBAN_ALL_CC_PAYMENT = 'unban all cc payments';

    public function label(): string
    {
        return match ($this) {
            self::SUSPEND => __('suspend'),
            self::UNSUSPEND => __('unsuspend'),
            self::BAN => __('ban'),
            self::UNBAN => __('unban'),
            self::WARNING_MESSAGE => __('warning message'),
            self::BAN_ALL_CC_PAYMENT => __('ban all cc payments'),
            self::UNBAN_ALL_CC_PAYMENT => __('unban all cc payments'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::SUSPEND => 'info',
            self::UNSUSPEND => 'indigo',
            self::BAN => __('danger'),
            self::UNBAN => __('success'),
            self::WARNING_MESSAGE => __('warning'),
            self::BAN_ALL_CC_PAYMENT => __('secondary'),
            self::UNBAN_ALL_CC_PAYMENT => __('primary'),
        };
    }
}
