<?php

namespace Aparlay\Core\Models\Enums;

enum OtpType: string implements Enum
{
    case EMAIL = 'email';
    case SMS = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => __('email'),
            self::SMS => __('sms'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::EMAIL => 'info',
            self::SMS => 'indigo',
        };
    }
}
