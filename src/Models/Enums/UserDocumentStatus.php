<?php

namespace Aparlay\Core\Models\Enums;

use Aparlay\Core\Models\UserDocument;

enum UserDocumentStatus: int implements Enum
{
    case CREATED = 0;
    case REJECTED = -1;
    case CONFIRMED = 1;

    public function label(): string
    {
        return match ($this) {
            self::CREATED => __('created'),
            self::REJECTED => __('rejected'),
            self::CONFIRMED => __('confirmed'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::REJECTED => 'danger',
            self::CREATED => 'warning',
            self::CONFIRMED => 'success',
        };
    }
}
