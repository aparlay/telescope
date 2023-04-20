<?php

namespace Aparlay\Core\Models\Enums;

enum ReportStatus: int implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::REPORTED => __('reported'),
            self::REVISED => __('revised'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::REPORTED => 'danger',
            self::REVISED => 'warning',
        };
    }

    case REPORTED = 0;
    case REVISED  = 1;
}
