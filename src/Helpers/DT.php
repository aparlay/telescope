<?php

namespace Aparlay\Core\Helpers;

use MongoDB\BSON\UTCDateTime;

class DT
{
    /**
     * @return UTCDateTime
     */
    public static function utcNow(): UTCDateTime
    {
        return new UTCDatetime();
    }

    /**
     * @param $config
     * @return UTCDateTime
     */
    public static function utcDateTime($config): UTCDateTime
    {
        $pastTimestampString[] = ($config['y'] ?? 0) . ' years';
        $pastTimestampString[] = ($config['mm'] ?? 0) . ' months';
        $pastTimestampString[] = ($config['w'] ?? 0) . ' weeks';
        $pastTimestampString[] = ($config['d'] ?? 0) . ' days';
        $pastTimestampString[] = ($config['h'] ?? 0) . ' hours';
        $pastTimestampString[] = ($config['m'] ?? 0) . ' minutes';
        $pastTimestampString[] = ($config['s'] ?? 0) . ' seconds';

        $pastTimestamp = strtotime(implode(' ', $pastTimestampString));

        return new UTCDatetime($pastTimestamp * 1000);
    }

    /**
     * @param $utcDateTime
     * @return float|int
     */
    public static function utcToTimestamp($utcDateTime)
    {
        return (string)$utcDateTime / 1000;
    }

    public static function strToUtc(string $offset)
    {
        return new UTCDatetime($offset);
    }

    public static function timestampToUtc($timestamp)
    {
        return new UTCDatetime($timestamp*1000);
    }

    public static function utcToDateTime(UTCDateTime $dateTime)
    {
        return $dateTime->toDateTime();
    }
}
