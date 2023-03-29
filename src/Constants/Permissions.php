<?php

namespace Aparlay\Core\Constants;

class Permissions
{
    const APPROVE_PAYOUTS = 'approve payouts';
    const LIST_PAYOUTS = 'list payouts';

    const VERIFY_WALLETS = 'verify wallets';
    const LIST_WALLETS = 'approve wallets';

    const VIEW_CHATS = 'view chats';
    const EDIT_CHATS = 'edit chats';
    const LIST_CHATS = 'list chats';

    const LIST_PAYERS = 'list payers';
    const VIEW_PAYERS = 'view payers';
    const UPDATE_PAYERS = 'update payers';

    const LIST_BROADCASTS = 'list broadcasts';
    const VIEW_BROADCASTS = 'view broadcasts';
    const DELETE_BROADCASTS = 'delete broadcasts';

    /**
     * @param $permission
     * @return string
     */
    public static function forRouter($permission): string
    {
        return 'permission:'.$permission;
    }
}
