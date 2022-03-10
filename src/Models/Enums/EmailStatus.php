<?php

namespace Aparlay\Core\Models\Enums;

enum EmailStatus: int implements Enum
{
    use EnumEnhancements;

    case QUEUED = 0;
    case SENT = 1;
    case OPENED = 2;
    case FAILED = 3;

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => __('queued'),
            self::SENT => __('sent'),
            self::OPENED => __('opened'),
            self::FAILED => __('failed'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::QUEUED => 'warning',
            self::SENT => 'info',
            self::OPENED => 'success',
            self::FAILED => 'danger',
        };
    }
}
