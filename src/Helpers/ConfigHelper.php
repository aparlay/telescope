<?php

namespace Aparlay\Core\Helpers;

use Aparlay\Core\Models\Setting;
use Illuminate\Support\Facades\Config;

class ConfigHelper
{
    public static function initialize(): array
    {
        $dbSettings = Setting::all([
            'title', 'value', 'group',
        ])
        ->groupBy('group')
        ->toArray();

        $configArray = [];
        foreach ($dbSettings as $key => $settings) {
            foreach ($settings as $setting) {
                Config::set($key.'.'.$setting['title'], $setting['value']);
            }
        }

        return $configArray;
    }
}
