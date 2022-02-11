<?php

namespace Aparlay\Core\Helpers;

use Illuminate\Support\Facades\Cache;

class Country
{
    public static function getAlpha2AndNames()
    {
        if (($countries = Cache::store('octane')->get('countries', false)) === false) {
            $countries = [];
            foreach (\Aparlay\Core\Models\Country::get() as $country) {
                $countries[$country->alpha2] = $country->name;
            }

            $countries = json_encode($countries);
            Cache::store('octane')->put('countries', $countries, 300);
        }

        return json_decode($countries, true);
    }

    public static function getFlagByAlpha2($alpha2, $size = '32')
    {
        $country = self::getByAlpha2($alpha2);

        return $country['flags:'.$size] ?? $country['flags:32'] ?? '';
    }

    public static function getNameByAlpha2($alpha2)
    {
        $country = self::getByAlpha2($alpha2);

        return $country['name'] ?? '`'.$alpha2.'` Not Found!';
    }

    public static function getAlpha3ByAlpha2($alpha2)
    {
        $country = self::getByAlpha2($alpha2);

        return $country['alpha3'];
    }

    /**
     * @param  string  $alpha2
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private static function getByAlpha2(string $alpha2): array
    {
        $alpha2 = \Str::lower($alpha2);
        $key = 'countries:'.$alpha2;
        $country = Cache::store('octane')->get($key, false);
        if ($country === false) {
            self::load();
            $country = Cache::store('octane')->get($key, false);
        }

        return json_decode($country);
    }

    /**
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private static function load()
    {
        foreach (\Aparlay\Core\Models\Country::get() as $country) {
            Cache::store('octane')->put('countries:'.$country->alpha2, json_encode([
                'alpha2' => $country->alpha2,
                'alpha3' => $country->alpha3,
                'name' => $country->name,
                'flags:16' => $country->flags['16'],
                'flags:24' => $country->flags['24'],
                'flags:32' => $country->flags['32'],
                'flags:48' => $country->flags['48'],
                'flags:64' => $country->flags['64'],
                'flags:128' => $country->flags['128'],
            ]), 300);
            Cache::store('octane')->put('countries:'.$country->alpha3, json_encode([
                'alpha2' => $country->alpha2,
                'alpha3' => $country->alpha3,
                'name' => $country->name,
                'flags:16' => $country->flags['16'],
                'flags:24' => $country->flags['24'],
                'flags:32' => $country->flags['32'],
                'flags:48' => $country->flags['48'],
                'flags:64' => $country->flags['64'],
                'flags:128' => $country->flags['128'],
            ]), 300);
        }
    }
}
