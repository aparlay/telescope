<?php

namespace Aparlay\Core\Models\Enums;

enum VersionOs: string implements Enum
{
    use EnumEnhancements;

    public function label(): string
    {
        return match ($this) {
            self::ANDROID => __('android'),
            self::IOS => __('ios'),
            self::WINDOWS => __('windows'),
            self::LINUX => __('linux'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ANDROID => 'warning',
            self::IOS => 'info',
            self::WINDOWS => 'success',
            self::LINUX => 'indigo',
        };
    }

    case ANDROID = 'android';
    case IOS     = 'ios';
    case WINDOWS = 'windows';
    case LINUX   = 'linux';
}
