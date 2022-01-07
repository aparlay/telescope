<?php

namespace Aparlay\Core\Models\Enums;

enum UserDocumentStatus: int implements Enum
{
    case PENDING = 1;
    case REJECTED = -1;
    case APPROVED = 2;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('pending'),
            self::REJECTED => __('rejected'),
            self::APPROVED => __('approved'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::REJECTED => 'danger',
            self::PENDING => 'warning',
            self::APPROVED => 'success',
        };
    }
}
