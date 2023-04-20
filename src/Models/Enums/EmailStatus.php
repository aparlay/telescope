<?php

namespace Aparlay\Core\Models\Enums;

enum EmailStatus: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => __('queued'),
            self::SENT => __('sent'),
            self::DELIVERED => __('delivered'),
            self::DEFERRED => __('delayed'),
            self::BOUNCED => __('bounced'),
            self::FAILED => __('failed'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::QUEUED => 'default',
            self::SENT => 'info',
            self::DELIVERED => 'success',
            self::DEFERRED => 'warning',
            self::BOUNCED => 'danger',
            self::FAILED => 'black',
        };
    }

    case QUEUED    = 0;
    case SENT      = 1;
    case DELIVERED = 2;
    case DEFERRED  = 3;
    case BOUNCED   = 4;
    case FAILED    = 5;
}
