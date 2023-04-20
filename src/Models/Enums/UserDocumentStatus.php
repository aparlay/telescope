<?php

namespace Aparlay\Core\Models\Enums;

enum UserDocumentStatus: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::CREATED => __('created'),
            self::PENDING => __('pending'),
            self::REJECTED => __('rejected'),
            self::APPROVED => __('approved'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::CREATED => 'info',
            self::REJECTED => 'danger',
            self::PENDING => 'warning',
            self::APPROVED => 'success',
        };
    }

    case CREATED  = 0;
    case PENDING  = 1;
    case REJECTED = -1;
    case APPROVED = 2;
}
