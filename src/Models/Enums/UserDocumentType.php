<?php

namespace Aparlay\Core\Models\Enums;

enum UserDocumentType: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::ID_CARD => __('id card'),
            self::SELFIE => __('selfie'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ID_CARD => 'info',
            self::SELFIE => 'success',
        };
    }

    case ID_CARD = 0;
    case SELFIE  = 1;
}
