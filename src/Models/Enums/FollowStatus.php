<?php

namespace Aparlay\Core\Models\Enums;

enum FollowStatus: int implements Enum
{
    case PENDING = 0;
    case ACCEPTED = 1;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('pending'),
            self::ACCEPTED => __('accepted'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'success',
        };
    }
}
