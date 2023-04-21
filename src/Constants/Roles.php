<?php

namespace Aparlay\Core\Constants;

final class Roles
{
    public const ADMINISTRATOR       = 'administrator';
    public const SUPER_ADMINISTRATOR = 'super-administrator';
    public const SUPPORT             = 'support';

    public static function forRouter(...$roles): string
    {
        return 'role:' . implode('|', $roles);
    }
}
