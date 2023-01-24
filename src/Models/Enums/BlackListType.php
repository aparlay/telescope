<?php

namespace Aparlay\Core\Models\Enums;

enum BlackListType: string implements Enum
{
    use EnumEnhancements;

    case USER_DEVICE_ID = 'user_device_id';
    case TEMPORARY_EMAIL_SERVICE = 'temporary_email_service';

    public function label(): string
    {
        return match ($this) {
            self::USER_DEVICE_ID => __('user'),
            self::TEMPORARY_EMAIL_SERVICE => __('media removed'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::USER_DEVICE_ID => 'warning',
            self::TEMPORARY_EMAIL_SERVICE => 'info'
        };
    }
}
