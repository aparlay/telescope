<?php

namespace Aparlay\Core\Models\Enums;

use Aparlay\Core\Models\UserDocument;

enum UserDocumentStatus: int implements Enum
{
    case PENDING = 0;
    case REJECTED = -1;
    case APPROVED = 1;

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
