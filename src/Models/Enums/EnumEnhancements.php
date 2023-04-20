<?php

namespace Aparlay\Core\Models\Enums;

trait EnumEnhancements
{
    /**
     * Returns enum values as an array.
     */
    public static function getAllCases(): array
    {
        $values = [];
        foreach (self::cases() as $enum) {
            $index          = $enum->value ?? $enum->name;
            $values[$index] = $enum->label();
        }

        return $values;
    }

    /**
     * Returns enum values as an array.
     */
    public static function getAllValues(): array
    {
        $values = [];
        foreach (self::cases() as $enum) {
            $values[] = $enum->value ?? $enum->name;
        }

        return $values;
    }

    /**
     * Returns enum values as a list.
     */
    public static function getValueList(string $separator = ', '): string
    {
        return implode($separator, self::getAllValues());
    }
}
