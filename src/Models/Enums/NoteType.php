<?php

namespace Aparlay\Core\Models\Enums;

use Aparlay\Core\Models\User;

enum NoteType: int implements Enum
{
    case SUSPEND = 1;
    case UNSUSPEND = 2;
    case BAN = 3;
    case UNBAN = 4;
    case WARNING_MESSAGE = 5;
    case BAN_ALL_CC_PAYMENT = 6;
    case UNBAN_ALL_CC_PAYMENT = 7;
    case OTHER = 8;

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
            self::OTHER => __('other')
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
            self::OTHER => __('info'),
        };
    }

    /**
     * @param  User  $admin
     * @param  User  $user
     * @return string
     */
    public function message(User $admin, User $user): string
    {
        return match ($this) {
            self::SUSPEND => __("User {$user->note_admin_url} is <b class='text-danger'>Freezed</b> by {$admin->note_admin_url}"),
            self::UNSUSPEND => __("User {$user->note_admin_url} is remove from <b class='text-success'>Freez</b> by {$admin->note_admin_url}"),
            self::BAN => __("User {$user->note_admin_url} is <b class='text-success'>Banned</b> by {$admin->note_admin_url}"),
            self::UNBAN => __("User {$user->note_admin_url} is <b class='text-success'>UnBanned</b> by {$admin->note_admin_url}"),
            self::BAN_ALL_CC_PAYMENT => __("User {$user->note_admin_url} is getting ban for any creditcard transaction by {$admin->note_admin_url}"),
            self::UNBAN_ALL_CC_PAYMENT => __("User {$user->note_admin_url} is getting remove from ban for any creditcard transaction by {$admin->note_admin_url}"),
            default => __("User {$user->note_admin_url} is getting unknown notes by {$admin->note_admin_url}"),
        };
    }

    /**
     * @param User $admin
     * @param User $user
     * @param $message
     */
    public function warningMessage(User $admin, User $user, $message)
    {
        return __("User {$user->note_admin_url} is getting <b class='text-warning'>Warning</b> with {$message} by {$admin->note_admin_url}");
    }

    /**
     * @param User $admin
     * @param User $user
     * @param $message
     */
    public function otherMessage(User $admin, User $user, $message)
    {
        return __("User {$user->note_admin_url} is getting a warning message {$message} by {$admin->note_admin_url}");
    }
}
