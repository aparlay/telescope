<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Alert as MediaBase;

class Alert extends MediaBase
{
    public static function getMediaMessages()
    {
        return [
            'app' => 'Your video has been removed due to copyright claims',
        ];
    }

    public static function getUserMessages()
    {
        return [
            'app' => 'You receive a notice due to users reports',
        ];
    }
}
