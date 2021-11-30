<?php

namespace Aparlay\Core\Admin\Observers;

use Aparlay\Core\Admin\Models\Setting;
use Aparlay\Core\Helpers\ConfigHelper;
use Aparlay\Core\Observers\BaseModelObserver;

class SettingObserver extends BaseModelObserver
{
    public function saved(Setting $setting)
    {
        ConfigHelper::loadConfig($setting->_id);
    }

    public function deleting(Setting $setting)
    {
        ConfigHelper::removeConfig($setting->_id);
    }
}
