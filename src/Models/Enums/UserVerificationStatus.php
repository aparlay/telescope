<?php

namespace Aparlay\Core\Models\Enums;

enum UserVerificationStatus: int implements Enum
{
    use EnumEnhancements;

    case REJECTED = -1;
    case UNVERIFIED = 1;
    case PENDING = 2;
    case UNDER_REVIEW = 3;
    case VERIFIED = 4;

    public function label(): string
    {
        return match ($this) {
            self::REJECTED => __('rejected'),
            self::UNVERIFIED => __('unverified'),
            self::PENDING => __('pending'),
            self::UNDER_REVIEW => __('under review'),
            self::VERIFIED => __('verified')
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::REJECTED => 'danger',
            self::UNVERIFIED => 'primary',
            self::PENDING => 'warning',
            self::UNDER_REVIEW => 'dark',
            self::VERIFIED => 'success'
        };
    }
}
