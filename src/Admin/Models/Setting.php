<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Scopes\SettingScope;
use Aparlay\Core\Models\Setting as BaseModel;

class Setting extends BaseModel
{
    use SettingScope;
}
