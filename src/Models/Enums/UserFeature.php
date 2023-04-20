<?php

namespace Aparlay\Core\Models\Enums;

enum UserFeature: string implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::TIPS => __('tips'),
            self::DEMO => __('demo'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::TIPS => 'warning',
            self::DEMO => 'info',
        };
    }

    case TIPS = 'tips';
    case DEMO = 'demo';
}
