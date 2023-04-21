<?php

namespace Aparlay\Core\Helpers;

class NumberHelper
{
    public static function shorten(int|float $number): string
    {
        $divisors = [
            1 => '',
            pow(1000, 1) => 'k',
            pow(1000, 2) => 'm',
            pow(1000, 3) => 'b',
            pow(1000, 4) => 't',
        ];

        foreach ($divisors as $divisor => $shorthand) {
            if (abs($number) < ($divisor * 1000)) {
                break;
            }
        }

        return number_format(floor($number / $divisor)) . $shorthand . (($divisor > 1) ? '+' : '');
    }
}
