<?php

namespace Aparlay\Core\Models\Enums;

enum UserVerificationStatus: int implements Enum
{
    case PENDING = 1;
    case VERIFIED = 2;
    case REJECTED = -1;
    case UNVERIFIED = 3;
    case UNDER_REVIEW = 4;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('pending'),
            self::VERIFIED => __('verified'),
            self::REJECTED => __('rejected'),
            self::UNVERIFIED => __('unverified'),
            self::UNDER_REVIEW => __('under review')
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
            self::UNVERIFIED => 'primary',
            self::UNDER_REVIEW => 'warning'
        };
    }
}
