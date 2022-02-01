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
            self::SUSPEND => __("User <b>{$user_username}</b> is getting suspended by <b>{$admin_username}</b>"),
            self::UNSUSPEND => __("User <b>{$user_username}</b> is remove from suspension by <b>{$admin_username}</b>"),
            self::BAN => __("User <b>{$user_username}</b> is getting ban by <b>{$admin_username}</b>"),
            self::UNBAN => __("User <b>{$user_username}</b> is remove from being ban by <b>{$admin_username}</b>"),
            self::WARNING_MESSAGE => __("User <b>{$user_username}</b> is getting a warning message by <b>{$admin_username}</b>"),
            self::BAN_ALL_CC_PAYMENT => __("User <b>{$user_username}</b> is getting ban for any creditcard transaction by <b>{$admin_username}</b>"),
            self::UNBAN_ALL_CC_PAYMENT => __("User <b>{$user_username}</b> is getting remove from ban for any creditcard transaction by <b>{$admin_username}</b>"),
        };
    }
}
