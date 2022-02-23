<?php

namespace Aparlay\Core\Constants;


class Permissions
{
    const APPROVE_PAYOUTS = 'approve payouts';
    const LIST_PAYOUTS = 'list payouts';

    /**
     * @param $permission
     * @return string
     */
    public static function forRouter($permission): string
    {
        return 'permission:' . $permission;
    }
}
