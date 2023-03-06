<?php

namespace Aparlay\Core\Models\Enums;

enum NoteCategory: int implements Enum
{
    use EnumEnhancements;

    case LOG = 1;
    case NOTE = 2;

    public function label(): string
    {
        return match ($this) {
            self::LOG => __('log'),
            self::NOTE => __('note'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::LOG => __('secondary'),
            self::NOTE => __('warning'),
        };
    }
}
