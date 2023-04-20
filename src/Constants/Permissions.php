<?php

namespace Aparlay\Core\Constants;

class Permissions
{
    public const APPROVE_PAYOUTS      = 'approve payouts';
    public const LIST_PAYOUTS         = 'list payouts';
    public const VERIFY_WALLETS       = 'verify wallets';
    public const LIST_WALLETS         = 'approve wallets';
    public const VIEW_CHATS           = 'view chats';
    public const EDIT_CHATS           = 'edit chats';
    public const LIST_CHATS           = 'list chats';
    public const OPEN_CHAT_AS_SUPPORT = 'chat as support';
    public const LIST_PAYERS          = 'list payers';
    public const VIEW_PAYERS          = 'view payers';
    public const UPDATE_PAYERS        = 'update payers';

    public static function forRouter($permission): string
    {
        return 'permission:' . $permission;
    }
}
