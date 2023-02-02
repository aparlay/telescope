<?php

namespace Aparlay\Core\Models\Enums;

enum BlackListType: string implements Enum
{
    use EnumEnhancements;

    case TEMPORARY_EMAIL_SERVICE = 'temporary_email_service';

    public function label(): string
    {
        return match ($this) {
            self::TEMPORARY_EMAIL_SERVICE => __('temporary email service'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::TEMPORARY_EMAIL_SERVICE => 'info'
        };
    }
}
