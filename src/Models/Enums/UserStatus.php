<?php

namespace Aparlay\Core\Models\Enums;

class UserStatus extends BaseEnum
{
    public const STATUS_PENDING = 0;
    public const STATUS_VERIFIED = 1;
    public const STATUS_ACTIVE = 2;
    public const STATUS_SUSPENDED = 3;
    public const STATUS_BLOCKED = 4;
    public const STATUS_DEACTIVATED = 10;
}
