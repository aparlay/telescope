<?php

namespace Aparlay\Core\Models\Enums;

enum AlertType: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::USER => __('user'),
            self::MEDIA_REMOVED => __('media removed'),
            self::MEDIA_NOTICED => __('media noticed'),
            self::USER_DOCUMENT_REJECTED => __('user document rejected'),
            self::USER_PAYOUT_DELETED => __('user payout deleted'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::USER => 'warning',
            self::MEDIA_REMOVED, self::USER_DOCUMENT_REJECTED, self::USER_PAYOUT_DELETED => 'danger',
            self::MEDIA_NOTICED => 'info',
        };
    }

    case USER                           = 0;
    case MEDIA_REMOVED                  = 20;
    case MEDIA_NOTICED                  = 21;
    case USER_DOCUMENT_REJECTED         = 50;
    case USER_PAYOUT_DELETED            = 30;
    case USER_PAYOUT_FAILED             = 31;
    case USER_PAYOUT_CANCELLED          = 32;
    case USER_PAYOUT_COMPLETED_MANUALLY = 33;
    case WALLET_REJECTED                = 40;
}
