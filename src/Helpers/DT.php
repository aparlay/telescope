<?php

namespace Aparlay\Core\Helpers;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

class DT
{
    /**
     * In this function we are using Carbon, so we could time-travel in tests
     * @return UTCDateTime
     */
    public static function utcNow(): UTCDateTime
    {
        return new UTCDatetime(Carbon::now()->valueOf());
    }

    /**
     * This is used instead of ::utcNow() or something different because
     * it allows to time-travel in the tests.
     * @param $offset_in_seconds
     * @return UTCDateTime
     */
    public static function utcDT($offset_in_seconds = 0): UTCDateTime
    {
        return new UTCDatetime(($offset_in_seconds * 1000) + Carbon::now()->valueOf());
    }

    public static function milliSecNow(): float
    {
        return (new Carbon())->valueOf();
    }

    public static function now(): string
    {
        return (new Carbon())->format('Y-m-d H:i:s');
    }

    /**
     * In this function we are using Carbon, so we could time-travel in tests
     * @param $config
     * @return UTCDateTime
     */
    public static function utcDateTime($config): UTCDateTime
    {
        $pastTimestampString[] = ($config['y'] ?? 0).' years';
        $pastTimestampString[] = ($config['mm'] ?? 0).' months';
        $pastTimestampString[] = ($config['w'] ?? 0).' weeks';
        $pastTimestampString[] = ($config['d'] ?? 0).' days';
        $pastTimestampString[] = ($config['h'] ?? 0).' hours';
        $pastTimestampString[] = ($config['m'] ?? 0).' minutes';
        $pastTimestampString[] = ($config['s'] ?? 0).' seconds';

        $pastTimestamp = Carbon::parse(implode(' ', $pastTimestampString))->timestamp;

        return new UTCDatetime($pastTimestamp * 1000);
    }

    /**
     * @param $utcDateTime
     *
     * @return int
     */
    public static function utcToTimestamp($utcDateTime)
    {
        return $utcDateTime / 1000;
    }

    /**
     * @param  UTCDateTime  $utcDateTime
     * @return Carbon
     */
    public static function utcToCarbon(UTCDateTime $utcDateTime): Carbon
    {
        return new Carbon($utcDateTime->toDateTime());
    }

    /**
     * @param  UTCDateTime  $utcDateTime
     * @return float
     */
    public static function utcToMillisec(UTCDateTime $utcDateTime): float
    {
        return (new Carbon($utcDateTime->toDateTime()))->valueOf();
    }

    public static function strToUtc(string $offset): UTCDateTime
    {
        return new UTCDatetime($offset);
    }

    public static function timestampToUtc($timestamp): UTCDateTime
    {
        return new UTCDatetime($timestamp * 1000);
    }

    public static function utcToDateTime(UTCDateTime $dateTime)
    {
        return $dateTime->toDateTime();
    }

    /**
     * @param int $millisec
     * @return UTCDateTime
     */
    public static function millisecToUtc(int $millisec): UTCDateTime
    {
        return new UTCDatetime($millisec);
    }
}
