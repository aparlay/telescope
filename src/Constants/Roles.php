<?php

namespace Aparlay\Core\Constants;

final class Roles
{
    const ADMIN = 'admin';
    const SUPER_ADMINISTRATOR = 'super-administrator';
    const SUPPORT = 'support';

    public static function forRouter(...$roles): string
    {
        return 'role:'.implode('|', $roles);
    }
}
