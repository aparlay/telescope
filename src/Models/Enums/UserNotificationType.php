<?php

namespace Aparlay\Core\Models\Enums;

enum UserNotificationType: string implements Enum
{
    use EnumEnhancements;

    case APPLICATION = 'application';
    case PUSH = 'push';
    case EMAIL = 'email';

    public function label(): string
    {
        return match ($this) {
            self::APPLICATION => __('application'),
            self::PUSH => __('push'),
            self::EMAIL => __('email'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::APPLICATION => 'info',
            self::PUSH => 'warning',
            self::EMAIL => 'success',
        };
    }
}
