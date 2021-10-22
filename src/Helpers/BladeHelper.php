<?php

namespace Aparlay\Core\Helpers;

class BladeHelper
{
    /**
     * @param $bytes
     * @return string
     */
    public static function fileSize($bytes)
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
