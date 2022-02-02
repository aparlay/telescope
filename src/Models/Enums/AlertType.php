<?php

namespace Aparlay\Core\Models\Enums;

enum AlertType: int implements Enum
{
    case USER = 0;
    case MEDIA_REMOVED = 20;
    case MEDIA_NOTICED = 21;

    case USER_DOCUMENT_REJECTED = 50;

    public function label(): string
    {
        return match ($this) {
            self::USER => __('user'),
            self::MEDIA_REMOVED => __('media removed'),
            self::MEDIA_NOTICED => __('media noticed'),
            self::USER_DOCUMENT_REJECTED => __('user document rejected')
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::USER => 'warning',
            self::MEDIA_REMOVED, self::USER_DOCUMENT_REJECTED => 'danger',
            self::MEDIA_NOTICED => 'info'
        };
    }
}
