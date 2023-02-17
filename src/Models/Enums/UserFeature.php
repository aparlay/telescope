<?php

namespace Aparlay\Core\Models\Enums;

enum UserFeature: string implements Enum
{
    use EnumEnhancements;

    case TIPS = 'tips';
    case DEMO = 'demo';
    const SUBSCRIPTIONS = 'subscriptions';

    public function label(): string
    {
        return match ($this) {
            self::TIPS => __('tips'),
            self::DEMO => __('demo'),
            self::SUBSCRIPTIONS => __('subscriptions')
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::SUBSCRIPTIONS => 'danger',
            self::TIPS => 'warning',
            self::DEMO => 'info',
        };
    }
}
