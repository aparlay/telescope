<?php

namespace Aparlay\Core\Helpers;

use Illuminate\Support\Facades\Cache;

class Country
{
    public static function getAlpha2AndNames()
    {
        if (($countries = Cache::store('octane')->get('countries', false)) === false) {
            $countries = [];
            foreach (\Aparlay\Core\Models\Country::query()->get() as $country) {
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

        return self::buildCountryFromCacheBy($alpha2);
    }

    /**
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private static function load()
    {
        foreach (\Aparlay\Core\Models\Country::query()->get() as $country) {
            Cache::store('octane')->put('countries:'.$country->alpha2.':alpha2', $country->alpha2, 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':alpha3', $country->alpha3, 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':name', $country->name, 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:16', 'https://flagcdn.com/16x12/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:24', 'https://flagcdn.com/24x18/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:32', 'https://flagcdn.com/32x24/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:48', 'https://flagcdn.com/48x36/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:64', 'https://flagcdn.com/64x48/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:128', 'https://flagcdn.com/128x96/'.$country->alpha2.'.png', 300);

            Cache::store('octane')->put('countries:'.$country->alpha3.':alpha2', $country->alpha2, 300);
            Cache::store('octane')->put('countries:'.$country->alpha3.':alpha3', $country->alpha3, 300);
            Cache::store('octane')->put('countries:'.$country->alpha3.':name', $country->name, 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:16', 'https://flagcdn.com/16x12/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:24', 'https://flagcdn.com/24x18/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:32', 'https://flagcdn.com/32x24/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:48', 'https://flagcdn.com/48x36/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:64', 'https://flagcdn.com/64x48/'.$country->alpha2.'.png', 300);
            Cache::store('octane')->put('countries:'.$country->alpha2.':flags:128', 'https://flagcdn.com/128x96/'.$country->alpha2.'.png', 300);
        }
    }

    public static function buildCountryFromCacheBy($alphaCode): array
    {
        if (Cache::store('octane')->get('countries:'.$alphaCode.':alpha2', false) === false) {
            self::load();
        }

        $country['alpha2'] = Cache::store('octane')->get('countries:'.$alphaCode.':alpha2', false);
        $country['alpha3'] = Cache::store('octane')->get('countries:'.$alphaCode.':alpha3', false);
        $country['name'] = Cache::store('octane')->get('countries:'.$alphaCode.':name', false);
        $country['flags:16'] = Cache::store('octane')->get('countries:'.$alphaCode.':flags:16', false);
        $country['flags:24'] = Cache::store('octane')->get('countries:'.$alphaCode.':flags:24', false);
        $country['flags:32'] = Cache::store('octane')->get('countries:'.$alphaCode.':flags:32', false);
        $country['flags:48'] = Cache::store('octane')->get('countries:'.$alphaCode.':flags:48', false);
        $country['flags:64'] = Cache::store('octane')->get('countries:'.$alphaCode.':flags:64', false);
        $country['flags:128'] = Cache::store('octane')->get('countries:'.$alphaCode.':flags:128', false);

        return $country;
    }
}
