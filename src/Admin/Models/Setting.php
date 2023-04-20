<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Scopes\SettingScope;
use Aparlay\Core\Models\Setting as BaseModel;
use OwenIt\Auditing\Contracts\Auditable;

class Setting extends BaseModel implements Auditable
{
    use SettingScope;
    use \OwenIt\Auditing\Auditable;
    public const VALUE_TYPE_STRING   = 0;
    public const VALUE_TYPE_BOOLEAN  = 1;
    public const VALUE_TYPE_INTEGER  = 2;
    public const VALUE_TYPE_DATETIME = 3;
    public const VALUE_TYPE_JSON     = 4;

    public static function getValueTypes()
    {
        return [
            self::VALUE_TYPE_STRING => __('String'),
            self::VALUE_TYPE_BOOLEAN => __('Boolean'),
            self::VALUE_TYPE_INTEGER => __('Integer'),
            self::VALUE_TYPE_DATETIME => __('Datetime'),
            self::VALUE_TYPE_JSON => __('JSON'),
        ];
    }
}
