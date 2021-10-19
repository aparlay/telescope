<?php

namespace Aparlay\Core\Admin\Models;

use Aparlay\Core\Models\Alert as MediaBase;

class Alert extends MediaBase
{
    public static function getMediaMessages()
    {
        return [
            'app' => 'Your video has been removed due to copyright claims',
            'app' => 'Your video has been removed due to blood and drug scense',
        ];
    }

    public static function getUserMessages()
    {
        return [
            'app' => 'You receive a notice due to users reports',
            'app' => 'You receive a notice due to so many fake traffic',
            'app' => 'You receive a notice due to fraud',
        ];
    }
}
