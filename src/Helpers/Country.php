<?php

namespace Aparlay\Core\Helpers;

use Illuminate\Support\Facades\Redis;

class Country
{
    public static function getAlpha2AndNames()
    {
        if (! Redis::exists('countries')) {
            foreach (\Aparlay\Core\Models\Country::get() as $country) {
                $countries[$country->alpha2] = $country->name;
            }

            Redis::hMSet('countries', $countries);
        }

        return Redis::hGetAll('countries');
    }

    public static function getFlagByAlpha2($alpha2, $size = '32')
    {
        $country = self::getByAlpha2($alpha2);

        return $country['flags:'.$size] ?? $country['flags:32'] ?? '';
    }

    public static function getNameByAlpha2($alpha2)
    {
        $country = self::getByAlpha2($alpha2);

        return $country['name'];
    }

    public static function getAlpha3ByAlpha2($alpha2)
    {
        $country = self::getByAlpha2($alpha2);

        return $country['alpha3'];
    }

    /**
     * @param  string  $alpha2
     * @return array
     */
    private static function getByAlpha2(string $alpha2): array
    {
        $key = 'countries:'.$alpha2;

        if (! Redis::exists($key)) {
            self::load();
        }

        return Redis::hGetAll($key);
    }

    /**
     * @return void
     */
    private static function load()
    {
        Redis::pipeline(function ($pipe) {
            foreach (\Aparlay\Core\Models\Country::get() as $country) {
                $pipe->hMSet('countries:'.$country->alpha2, [
                    'alpha2' => $country->alpha2,
                    'alpha3' => $country->alpha3,
                    'name' => $country->name,
                    'flags:16' => $country->flags['16'],
                    'flags:24' => $country->flags['24'],
                    'flags:32' => $country->flags['32'],
                    'flags:48' => $country->flags['48'],
                    'flags:64' => $country->flags['64'],
                    'flags:128' => $country->flags['128'],
                ]);
                $pipe->hMSet('countries:'.$country->alpha3, [
                    'alpha2' => $country->alpha2,
                    'alpha3' => $country->alpha3,
                    'name' => $country->name,
                    'flags:16' => $country->flags['16'],
                    'flags:24' => $country->flags['24'],
                    'flags:32' => $country->flags['32'],
                    'flags:48' => $country->flags['48'],
                    'flags:64' => $country->flags['64'],
                    'flags:128' => $country->flags['128'],
                ]);
            }
        });
    }
}
