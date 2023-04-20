<?php

namespace Aparlay\Core\Models\Enums;

use Aparlay\Core\Models\User;

enum NoteType: int implements Enum
{
    use EnumEnhancements;

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
            self::OTHER => __('other'),
            self::PUBLIC => __('public'),
            self::PRIVATE => __('private'),
            self::INVISIBLE_BY_ADMIN => __('invisible_by_admin'),
            self::SET_BAN_PAYOUT => __('set_ban_payout'),
            self::UNSET_BAN_PAYOUT => __('unset_ban_payout'),
            self::SET_AUTO_BAN_PAYOUT => __('set_auto_ban_payout'),
            self::UNSET_AUTO_BAN_PAYOUT => __('unset_auto_ban_payout'),
            self::SET_PASSWORD => __('set_password'),
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
            self::PUBLIC => __('success'),
            self::PRIVATE => __('warning'),
            self::INVISIBLE_BY_ADMIN => __('danger'),
            self::SET_BAN_PAYOUT => __('danger'),
            self::UNSET_BAN_PAYOUT => __('success'),
            self::SET_AUTO_BAN_PAYOUT => __('danger'),
            self::UNSET_AUTO_BAN_PAYOUT => __('success'),
            self::SET_PASSWORD => __('primary'),
        };
    }

    public function message(User $admin, User $user): string
    {
        return match ($this) {
            self::SUSPEND => __("User {$user->note_admin_url} is <b class='text-warning'>Suspended</b> by {$admin->note_admin_url}"),
            self::UNSUSPEND => __("User {$user->note_admin_url} is <b class='text-success'>Unsuspended</b> by {$admin->note_admin_url}"),
            self::BAN => __("User {$user->note_admin_url} is <b class='text-success'>Banned</b> by {$admin->note_admin_url}"),
            self::UNBAN => __("User {$user->note_admin_url} is <b class='text-success'>UnBanned</b> by {$admin->note_admin_url}"),
            self::BAN_ALL_CC_PAYMENT => __("User {$user->note_admin_url} is banned for any credit-card transaction by {$admin->note_admin_url}"),
            self::UNBAN_ALL_CC_PAYMENT => __("User {$user->note_admin_url} is unbanned for any credit-card transaction by {$admin->note_admin_url}"),
            self::PUBLIC => __("User {$user->note_admin_url} is <b class='text-success'>set to public</b> by {$admin->note_admin_url}"),
            self::PRIVATE => __("User {$user->note_admin_url} is <b class='text-warning'>set to private</b> by {$admin->note_admin_url}"),
            self::INVISIBLE_BY_ADMIN => __("User {$user->note_admin_url} is <b class='text-danger'>set invisible</b> by {$admin->note_admin_url}"),
            self::SET_BAN_PAYOUT => __("User {$user->note_admin_url} payout is <b class='text-danger'>Banned</b> by {$admin->note_admin_url}"),
            self::UNSET_BAN_PAYOUT => __("User {$user->note_admin_url} payout is <b class='text-success'>UnBanned</b> by {$admin->note_admin_url}"),
            self::SET_AUTO_BAN_PAYOUT => __("User {$user->note_admin_url} auto payout is <b class='text-danger'>Banned</b> by {$admin->note_admin_url}"),
            self::UNSET_AUTO_BAN_PAYOUT => __("User {$user->note_admin_url} auto payout is <b class='text-success'>UnBanned</b> by {$admin->note_admin_url}"),
            self::SET_PASSWORD => __("User {$user->note_admin_url} password <b class='text-primary'>Set</b> by {$admin->note_admin_url}"),
            default => __("User {$user->note_admin_url} is received unknown notes by {$admin->note_admin_url}"),
        };
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function warningMessage(User $admin, User $user, $message)
    {
        return __("User {$user->note_admin_url} is getting <b class='text-warning'>Warning</b> with <em>\"{$message}\"</em> by {$admin->note_admin_url}");
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function otherMessage(User $admin, User $user, $message)
    {
        return __("<em>\"{$message}\"</em> by {$admin->note_admin_url}");
    }

    case SUSPEND               = 1;
    case UNSUSPEND             = 2;
    case BAN                   = 3;
    case UNBAN                 = 4;
    case WARNING_MESSAGE       = 5;
    case BAN_ALL_CC_PAYMENT    = 6;
    case UNBAN_ALL_CC_PAYMENT  = 7;
    case OTHER                 = 8;
    case PUBLIC                = 9;
    case PRIVATE               = 10;
    case SET_BAN_PAYOUT        = 11;
    case UNSET_BAN_PAYOUT      = 12;
    case SET_AUTO_BAN_PAYOUT   = 13;
    case UNSET_AUTO_BAN_PAYOUT = 14;
    case INVISIBLE_BY_ADMIN    = 15;
    case SET_PASSWORD          = 16;
}
