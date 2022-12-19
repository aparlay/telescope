<?php

namespace Aparlay\Core\Models\Enums;

enum EmailType: int implements Enum
{
    use EnumEnhancements;

    case OTP = 0;
    case CONTACT = 1;
    case ACCOUNT_VERIFICATION = 2;

    public function label(): string
    {
        return match ($this) {
            self::OTP => __('otp'),
            self::CONTACT => __('contact'),
            self::ACCOUNT_VERIFICATION => __('account verification'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::OTP => 'info',
            self::CONTACT => 'indigo',
            self::ACCOUNT_VERIFICATION => 'danger',
        };
    }
}
