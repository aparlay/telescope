<?php

namespace Aparlay\Core\Constants;

class Permissions
{
    const APPROVE_PAYOUTS = 'approve payouts';
    const LIST_PAYOUTS = 'list payouts';

    const VERIFY_WALLETS = 'verify wallets';
    const LIST_WALLETS = 'approve wallets';

    const VIEW_CHATS = 'view chats';
    const LIST_CHATS = 'list chats';

    /**
     * @param $permission
     * @return string
     */
    public static function forRouter($permission): string
    {
        return 'permission:'.$permission;
    }
}
