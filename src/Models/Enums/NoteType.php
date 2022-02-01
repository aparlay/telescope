<?php

namespace Aparlay\Core\Models\Enums;

enum NoteType: int implements Enum
{
    case SUSPEND = 1;
    case UNSUSPEND = 2;
    case BAN = 3;
    case UNBAN = 4;
    case WARNING_MESSAGE = 5;
    case BAN_ALL_CC_PAYMENT = 6;
    case UNBAN_ALL_CC_PAYMENT = 7;

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
            self::SUSPEND => __('info'),
            self::UNSUSPEND => __('indigo'),
            self::BAN => __('danger'),
            self::UNBAN => __('success'),
            self::WARNING_MESSAGE => __('warning'),
            self::BAN_ALL_CC_PAYMENT => __('secondary'),
            self::UNBAN_ALL_CC_PAYMENT => __('primary'),
        };
    }

    public function message($user_username, $admin_username): string
    {
        return match ($this) {
            self::SUSPEND => __("User {$user_username} is getting suspended by {$admin_username}"),
            self::UNSUSPEND => __("User {$user_username} is remove from suspension by {$admin_username}"),
            self::BAN => __("User {$user_username} is getting ban by {$admin_username}"),
            self::UNBAN => __("User {$user_username} is remove from being ban by {$admin_username}"),
            self::WARNING_MESSAGE => __("User {$user_username} is getting a warning message by {$admin_username}"),
            self::BAN_ALL_CC_PAYMENT => __("User {$user_username} is getting ban for any creditcard transaction by {$admin_username}"),
            self::UNBAN_ALL_CC_PAYMENT => __("User {$user_username} is getting remove from ban for any creditcard transaction by {$admin_username}"),
        };
    }
}
