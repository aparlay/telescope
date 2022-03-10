<?php

namespace Aparlay\Core\Models\Enums;

enum EmailType: int implements Enum
{
    use EnumEnhancements;

    case OTP = 0;
    case CONTACT = 1;

    public function label(): string
    {
        return match ($this) {
            self::OTP => __('otp'),
            self::CONTACT => __('contact'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::OTP => 'info',
            self::CONTACT => 'indigo',
        };
    }
}
