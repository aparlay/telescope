<?php

namespace Aparlay\Core\Models\Enums;


enum PayoutStatus: int implements Enum
{
    use EnumEnhancements;

    case CREATED = 1;
    case IN_PROGRESS = 2;
    case SENT = 3;

    case COMPLETED = 8;

    public function label(): string
    {
        return match ($this) {
            self::CREATED => __('created'),
            self::IN_PROGRESS => __('user made payout request'),
            self::SENT => __('sent'),
            self::COMPLETED => __('completed'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::CREATED => 'info',
            self::SENT => 'info',
            self::IN_PROGRESS => 'warning',
            self::COMPLETED => 'success',
        };
    }
}
