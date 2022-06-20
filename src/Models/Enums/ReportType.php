<?php

namespace Aparlay\Core\Models\Enums;

enum ReportType: int implements Enum
{
    use EnumEnhancements;

    case USER = 0;
    case MEDIA = 1;
    case COMMENT = 2;


    public function label(): string
    {
        return match ($this) {
            self::USER => __('user'),
            self::MEDIA => __('media'),
            self::COMMENT => __('comment'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::USER => 'info',
            self::MEDIA => 'indigo',
            self::COMMENT => 'primary',
        };
    }
}
