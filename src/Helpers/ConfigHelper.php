<?php

namespace Aparlay\Core\Helpers;

use Aparlay\Core\Models\Setting;
use Illuminate\Support\Facades\Config;

class ConfigHelper
{
    public static function loadDbConfig(): void
    {
        $dbSettings = Setting::all([
            'title', 'value', 'group',
        ])
            ->groupBy('group')
            ->toArray();

        foreach ($dbSettings as $key => $settings) {
            foreach ($settings as $setting) {
                Config::set($key . '.' . $setting['title'], $setting['value']);
            }
        }
    }

    public static function loadConfig($id)
    {
        $setting = Setting::find($id);

        Config::set($setting->group . '.' . $setting->title, $setting->value);
    }

    public static function removeConfig($id)
    {
        $setting = Setting::find($id);

        Config::offsetUnset($setting->group . '.' . $setting->title);
    }
}
