<?php

namespace Aparlay\Core\Models\Enums;

enum UserDocumentStatus: int implements Enum
{
    case CREATED = 0;
    case CONFIRMED = 1;

    public function label(): string
    {
        return match ($this) {
            self::CREATED => __('created'),
            self::CONFIRMED => __('confirmed'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::CREATED => 'warning',
            self::CONFIRMED => 'success',
        };
    }
}
