<?php

namespace Aparlay\Core\Models\Enums;

enum UserDocumentType: int implements Enum
{
    use EnumEnhancements;

    case ID_CARD = 0;
    case VIDEO_SELFIE = 1;

    public function label(): string
    {
        return match ($this) {
            self::ID_CARD => __('id card'),
            self::VIDEO_SELFIE => __('selfie'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ID_CARD => 'info',
            self::VIDEO_SELFIE => 'success',
        };
    }
}
